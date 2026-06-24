      </main>
    </div>
  </div>

  <script src="assets/vendors/jquery/jquery-3.4.1.js"></script>
  <script src="assets/js/admin-panel.js"></script>
  <?php if (!empty($adminExtraJs) && is_array($adminExtraJs)) : ?>
    <?php foreach ($adminExtraJs as $jsFile) : ?>
      <script src="<?php echo htmlspecialchars($jsFile); ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
