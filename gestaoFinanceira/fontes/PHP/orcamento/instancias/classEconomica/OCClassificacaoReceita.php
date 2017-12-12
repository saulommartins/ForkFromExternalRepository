<?php
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
?>
<?php
/**
    * Arquivo Oculto - Classificação Receita
    * Data de Criação   : 21/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    $Id: OCClassificacaoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.04
*/

/*
$Log$
Revision 1.6  2007/07/03 19:18:00  cako
Bug#9466#

Revision 1.5  2007/06/25 15:43:17  vitor
Bug#9466#

Revision 1.4  2006/07/05 20:42:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
    case "mascaraClassificacao":

      if (($_POST['inCodClassificacao']{0} == 1) OR
         ($_POST['inCodClassificacao']{0} == 2) OR
         ($_POST['inCodClassificacao']{0} == 7) OR
         ($_POST['inCodClassificacao']{0} == 8) OR
         (((Sessao::getExercicio() < 2008 && $_POST['inCodClassificacao']{0}) == 9 ? true : false) )) {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'], $_POST['inCodClassificacao'] );
            $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."';
                    f.Ok.disabled = false; \n";
      } else {
          $js .= "  var mensagem = '';
                    mensagem += '@Informe apenas classificações dos grupos 1,2,7 e 8.';
                    alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
                    erro=true

                    f.inCodClassificacao.value = '';
                    f.inCodClassificacao.focus();
                    f.Ok.disabled = false;
                 ";
      }
        SistemaLegado::executaFrameOculto( $js );
    break;
}
?>
