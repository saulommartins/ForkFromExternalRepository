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
   * Oculto de Relatório de Concessão de Vale-Tranporte
   * Data de Criação: 07/11/2005

   * @author Analista: Diego Victoria
   * @author Desenvolvedor: Leandro André Zis

   * Casos de uso: uc-03.03.05
                   uc-03.04.03

   $Id: OCIMontaCatalogoClassificacao.php 59612 2014-09-02 12:00:51Z gelson $

   */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GP_ALM_COMPONENTES."IMontaClassificacao.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php";

$obIMontaClassificacao = Sessao::read("objMontaClassificacao");

switch ($_REQUEST["stCtrl"]) {

   case "montaClassificacao":

      # Validaçao necessaria para nao prossegir sem que tenha um valor na variavel.
      if (is_object($obIMontaClassificacao) && (int) !empty($_GET['inCodCatalogo'])) {
         $obFormulario   = new Formulario;
         $obIMontaClassificacao->setCodigoCatalogo( $_GET['inCodCatalogo'] );

         if ($obIMontaClassificacao->getReadOnly()) {
            $obIMontaClassificacao->geraFormularioReadOnly( $obFormulario, $_REQUEST['inNumNiveisClassificacao']);
         } else {
            $obIMontaClassificacao->geraFormulario( $obFormulario );
         }

         $obFormulario->montaInnerHTML();
         $stJs = ' d.getElementById(\'spnClassificacao\').innerHTML = \''.$obFormulario->getHtml() .'\';';

         if (!$obIMontaClassificacao->getReadOnly()) {
            $obFormulario->obJavaScript->montaJavaScript();
            $stValida = $obFormulario->obJavaScript->getInnerJavaScript();
            $stJs .= " f.stValida.value = '".$stValida."';";

            if ($obIMontaClassificacao->stCodEstruturalReduzido)
               if (Sessao::read('carregarCombo') == true) {
                  $stJs .=  $obIMontaClassificacao->preencheCombosClassificacao($_REQUEST['inNumNiveisClassificacao']);
                  Sessao::remove('carregarCombo');
               }
         }

         $stJs .=  $obIMontaClassificacao->stOnChangeCombo;
      } else {
         $stJs = ' d.getElementById(\'spnClassificacao\').innerHTML = \'\';';
      }
   break;
}

if (!empty($stJs))
   echo $stJs;
