<?php
// Данные из cookies
$errors = [];
$oldValues = [];
$savedValues = [];

if (isset($_COOKIE['form_errors'])) {
    $errors = json_decode($_COOKIE['form_errors'], true);
    $oldValues = json_decode($_COOKIE['old_values'], true);
}
foreach ($_COOKIE as $name => $value) {
    if (strpos($name, 'saved_') === 0) {
        $field = substr($name, 6);
        $savedValues[$field] = $value;
    }
}

// Получение значения поля
function getFieldValue($field, $default = '') {
    global $oldValues, $savedValues;

    if (isset($oldValues[$field])) {
        return $oldValues[$field];
    }

    if (isset($savedValues[$field])) {
        return $savedValues[$field];
    }

    return $default;
}

// Функция для проверки
function isSelected($field, $value) {
    global $oldValues, $savedValues;

    $currentValues = [];
    if (isset($oldValues[$field])) {
        if ($field === 'languages') {
            $currentValues = explode(',', $oldValues[$field]);
        } else {
            return $oldValues[$field] === $value ? 'checked' : '';
        }
    } elseif (isset($savedValues[$field])) {
        if ($field === 'languages') {
            $currentValues = explode(',', $savedValues[$field]);
        } else {
            return $savedValues[$field] === $value ? 'checked' : '';
        }
    }

    return in_array($value, $currentValues) ? 'selected' : '';
}

// Проверка чекбокса
function isChecked($field) {
    global $oldValues, $savedValues;

    if (isset($oldValues[$field])) {
        return $oldValues[$field] ? 'checked' : '';
    }

    if (isset($savedValues[$field])) {
        return $savedValues[$field] ? 'checked' : '';
    }

    return '';
}
?>
<!DOCTYPE html>

<html lang="ru-RU">

  <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   <meta charset="UTF-8">
    <title>index</title>
  </head>
  <body>
<div class="form1">
    <h3>Форма:</h3>
    <?php if (!empty($messages)): ?>
           <div class="mb-3">
               <?php foreach ($messages as $message): ?>
                   <div class="alert alert-info"><?= $message ?></div>
               <?php endforeach; ?>
           </div>
       <?php endif; ?>

       <?php
       $has_errors = false;
       foreach ($errors as $error) {
           if (!empty($error)) {
               $has_errors = true;
               break;
           }
       }
       ?>

       <?php if ($has_errors): ?>
           <div class="alert alert-danger mb-3">
               <h4>Обнаружены ошибки:</h4>
               <ul class="mb-0">
                   <?php foreach ($errors as $field => $error): ?>
                       <?php if (!empty($error)): ?>
                           <li><?= htmlspecialchars($error) ?></li>
                       <?php endif; ?>
                   <?php endforeach; ?>
               </ul>
           </div>
       <?php endif; ?>
        <label>
            Текстовое поле ФИО:<br />
            <input placeholder="Введите ФИО" name="name" id = "name" required
            value="<?php echo htmlspecialchars($values['name'] ?? ''); ?>">
                    <?php if (!empty($errors['name'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
        </label><br /><br />


        <label>
            Поле tel:<br />
            <input type="number" name="phone" id="phone" required
            value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>"
                           class="<?php echo !empty($errors['phone']) ? 'is-invalid' : ''; ?>">
                           <?php if (!empty($errors['phone'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['phone']); ?></div>
                    <?php endif; ?>
        </label><br /><br />

        <label>
            Текстовое поле email:<br>
            <input name="email" id="email" type="email" placeholder="Введите вашу почту" required
            value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>"
                           class="<?php echo !empty($errors['email']) ? 'is-invalid' : ''; ?>">
                    <?php if (!empty($errors['birthdate'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
        </label><br /><br />

        <label>
            Поле даты рождения:<br>
            <input name="birthdate" id="birthdate" value="2005-09-11" type="date" required
            value="<?php echo htmlspecialchars($values['birthdate'] ?? ''); ?>"
                           class="<?php echo !empty($errors['birthdate']) ? 'is-invalid' : ''; ?>">
                     <?php if (!empty($errors['birthdate'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['birthdate']); ?></div>
                    <?php endif; ?>
        </label><br /><br />


        Радиокнопки (пол):<br />
        <label>
            <input type="radio" checked="checked"
                   id="male" name="gender" value="male" required
                   <?php echo ($values['gender'] ?? '') === 'male' ? 'checked' : ''; ?>
                               class="<?php echo !empty($errors['gender']) ? 'is-invalid' : ''; ?>">
            Мужской
        </label>
        <label>
            <input type="radio"
                   id="female" name="gender" value="female" <?php echo isSelected('gender', 'female'); ?>
                   <?php echo ($values['gender'] ?? '') === 'female' ? 'checked' : ''; ?>
                               class="<?php echo !empty($errors['gender']) ? 'is-invalid' : ''; ?>">
            Женский
        </label><br /><br>
        <?php if (!empty($errors['gender'])): ?>
                <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['gender']); ?></div>
            <?php endif; ?>
        <label>
            Любимый язык программирования: (listbox с множественным выбором):
            <br>
            <select id="languages" name="languages[]" multiple="multiple" required class="<?php echo !empty($errors['languages']) ? 'is-invalid' : ''; ?>" size="5">
            <?php
              $allLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
              $selectedLanguages = isset($values['languages']) ? (is_array($values['languages']) ? $values['languages'] : explode(',', $values['languages'])) : [];

              foreach ($allLanguages as $lang): ?>
                  <option value="<?php echo htmlspecialchars($lang); ?>"
                      <?php echo in_array($lang, $selectedLanguages) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($lang); ?>
                  </option>
              <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['languages'])): ?>
              <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['languages']); ?></div>
          <?php endif; ?>
        </label><br /><br />

        <label>
            Биография (многострочное текстовое поле):<br>
            <textarea id="bio" name="bio" required
            class="<?php echo !empty($errors['bio']) ? 'is-invalid' : ''; ?>"><?php
                              echo htmlspecialchars($values['bio'] ?? ''); ?></textarea>
                      <?php if (!empty($errors['bio'])): ?>
                          <div class="invalid-feedback"><?php echo htmlspecialchars($errors['bio']); ?></div>
                      <?php endif; ?>
        </label> <br /><br />



        "Чекбокс:"
        <br />
        <label>
            <input type="checkbox" checked="checked" name="contract_accepted" id="contract_accepted" required
            class="<?php echo !empty($errors['agreement']) ? 'is-invalid' : ''; ?>">
            <?php echo ($values['agreement'] ?? '') ? 'checked' : ''; ?>
            С контрактом ознакомлен(а)
            <?php if (!empty($errors['agreement'])): ?>
                <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['agreement']); ?></div>
            <?php endif; ?>
        </label>

        <br /><br />
        <input type="submit" value="Сохранить" name="save">

        <?php if (!empty($_SESSION['login'])): ?>
                <a href="logout.php" class="btn btn-danger">Выйти</a>
            <?php endif; ?>
    </form>
</div>
</body>
</html>
