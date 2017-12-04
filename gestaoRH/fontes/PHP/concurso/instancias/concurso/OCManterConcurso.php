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
* Página de Formulario de Inclusao/Alteracao de Concurso
* Data de Criação: 28/06/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Cristiano Brasil Sperb

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_CON_NEGOCIO."RConcursoConcurso.class.php" 		);
include_once(CLA_ARQUIVO);

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$obRConcurso = new RConcursoConcurso;
$stEdital = $_POST["stEdital"];
$stTipoNorma = $_POST["stTipoNorma"];
$nuNotaMinima = $_POST["nuNotaMinima"];

switch ($stCtrl) {
   case "buscaNormaDesteTipo":
      $js  = "limpaSelect(f.stNorma,0); \n";
      $js .= "f.inCodigoNorma.value = ''; \n";
      $js .= "f.stNorma.options[0] = new Option('Selecione a Norma','', 'selected');\n";
      if ($stTipoNorma != "") {
     $obRConcurso->obRNorma->obRTipoNorma->setCodTipoNorma($stTipoNorma);
     $obRConcurso->obRNorma->listar($rsNormas,"");
     $inContador = 1;
        while (!$rsNormas->eof()) {
           $stNorma = $rsNormas->getCampo("cod_norma");
           $stNomNorma = $rsNormas->getCampo("nom_norma");
           $js .= "f.stNorma.options[$inContador] = new Option('".$stNomNorma."','".$stNorma."'); \n";
           $inContador++;
           $rsNormas->proximo();
        }
      }
      SistemaLegado::executaFrameOculto($js);
   break;

   case "buscaLinkNorma":
      if ($stNorma != "") {
     $obRConcurso->obRNorma->setCodNorma($stNorma);
     $obRConcurso->obRNorma->listar($rsNormas,"");
     //$stUrl= $rsNormas->getCampo("link");
     $stUrl="<a href=".$rsNormas->getCampo("link")." target=blank_ >".$rsNormas->getCampo("link")."</a>";
     $js .="d.getElementById('spnlinkNormaRegulamentadora').innerHTML='".$stUrl."' \n";
     SistemaLegado::executaFrameOculto($js);
      }
   break;

   case "buscaLinkEdital":
      if ($stEdital != "") {
         $obRConcurso->obRNorma->setCodNorma($stEdital);
         $obRConcurso->obRNorma->listar($rsNormas,"");

         $stNomeArquivo = $rsNormas->getCampo("link");
         $stDirAnexos = CAM_NORMAS.'anexos/';
         $stCaminhoCompleto = $stDirAnexos . $stNomeArquivo;

         $stUrl="<a href=".$stCaminhoCompleto." target=oculto >".$rsNormas->getCampo("link")."</a>";

         $js .="d.getElementById('spnlinkEdital').innerHTML='".$stUrl."' \n";
         $js .="f.dtPublicacao.value = '".$rsNormas->getCampo("dt_publicacao")."'\n";
         $js .="d.getElementById('lbldtPublicacao').innerHTML = '".$rsNormas->getCampo("dt_publicacao")."' \n";

         $js .="f.stTipoNorma.value = '".$rsNormas->getCampo("cod_tipo_norma")."'\n";
         $js .="f.inCodigoTipoNorma.value = '".$rsNormas->getCampo("cod_tipo_norma")."'\n";

         $inContador = 1;
        while (!$rsNormas->eof()) {
           $stNorma = $rsNormas->getCampo("cod_norma");
           $stNomNorma = $rsNormas->getCampo("nom_norma");
           $js .= "f.stNorma.options[$inContador] = new Option('".$stNomNorma."','".$stNorma."'); \n";
           $inContador++;
           $rsNormas->proximo();
        }

      }
      SistemaLegado::executaFrameOculto($js);
   break;

   case "buscaLinkEditalHomologacao":
      if ($stEditalHomologacao != "") {
         $obRConcurso->obRNorma->setCodNorma($stEditalHomologacao);
         $obRConcurso->obRNorma->listar($rsNormas,"");
         $js .="f.dtHomologacao.value = '".$rsNormas->getCampo("dt_publicacao")."'\n";
         $js .="d.getElementById('lbldtHomologacao').innerHTML='".$rsNormas->getCampo('dt_publicacao')."' \n";
      } else {
         $js .="f.dtHomologacao.value = ''\n";
         $js .="d.getElementById('lbldtHomologacao').innerHTML='' \n";
      }
      SistemaLegado::executaFrameOculto($js);
   break;

   case "validaCampo":
      if ($nuNotaMinima==0) {
     $js .= "f.nuNotaMinima.value = '' \n";
         SistemaLegado::executaFrameOculto($js);
      }
   break;
}
?>
