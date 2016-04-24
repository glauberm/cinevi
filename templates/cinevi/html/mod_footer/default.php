<?php

defined('_JEXEC') or die;
?>

<div class="clearfix">
    <div class="pull-left">
        <div class="footer1<?php echo $moduleclass_sfx ?>"><?php echo $lineone; ?></div>
    </div>
    <!-- Button trigger modal -->
    <div class="pull-right">
        <ul class="list-inline">
            <li><a data-toggle="modal" data-target="#modalLicenca">Licença</a></li>
            <li><a data-toggle="modal" data-target="#modalCreditos">Créditos</a></li>
        </ul>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modalCreditos" tabindex="-1" role="dialog" aria-labelledby="modalCreditos">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalCreditos">Créditos</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled row">
                    <li class="col-md-4">
                        <strong>Desenvolvido por Glauber Mota</strong>
                        <ul>
                            <li>glaubernm [arroba] gmail [ponto] com</li>
                            <li>(21) 9.9796-3685</li>
                        </ul>
                    </li>
                    <li class="col-md-4">
                        Quer contribuir? Participe da discussão no <a href="https://github.com/glauberm/cinevi" target="_blank">GitHub</a>.
                    </li>
                    <li class="col-md-4">
                        <ul>
                            <li>Fonte dos Títulos: <a href="https://www.google.com/fonts/specimen/Buenard" target="_blank">Buenard</a> por Google Fonts.</li>
                            <li>Ícone dos Documentos por <a href="http://www.flaticon.com/" target="_blank">Flaticon</a>.</li>
                            <li><div class="footer2<?php echo $moduleclass_sfx ?>"><?php echo JText::_('MOD_FOOTER_LINE2'); ?></div></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLicenca" tabindex="-1" role="dialog" aria-labelledby="modalLicenca">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalLicenca">Licença</h4>
            </div>
            <div class="modal-body">
                Este é um Software Livre liberado sob os termos da <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">Licença Pública Geral GNU</a>.
            </div>
        </div>
    </div>
</div>
