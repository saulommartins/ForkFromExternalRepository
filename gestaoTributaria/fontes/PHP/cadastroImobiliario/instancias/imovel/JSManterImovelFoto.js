<script type="text/javascript">
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
</script>
<?php
/**
 *  * Funções javascript para o cadastro de fotos de imóveis
 *  *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * @author     Desenvolvedor Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * $Id:$
*/
?>
<script type="text/javascript">
function  montaSpanFileBox(){
    jQuery.post("<?=$pgOcul;?>", { stCtrl: "montaFileBox",inNumeroFoto: jQuery('#inNumeroFoto').val() }, 
    function(data) {
        jQuery('#spnFileBox').html(data);
    }
    , "html");
}

function excluirImagem(inCodFoto){
    jQuery('#stAcao').val('excluir');
    jQuery('#inCodFoto').val(inCodFoto);
    stAction='&chamada=ccform&stDescQuestao=Confirma excluir a foto('+inCodFoto+')?';
    alertaQuestao(stAction,'ccform','<?=Sessao::getId();?>');
    removeConfirmImagemPopUp();
}

</script>
