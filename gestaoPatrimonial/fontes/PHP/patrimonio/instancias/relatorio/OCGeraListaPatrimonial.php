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
    * Página de geração de relatório
    * Data de criação : 27/09/2006

    * @author Analista:
    * @author Programador: Lucas Teixeira Stephano

    Caso de uso: uc-03.01.21

    $Id: OCGeraListaPatrimonial.php 59612 2014-09-02 12:00:51Z gelson $

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";

$obRelatorio = new PreviewBirt(3,6,12);
$obRelatorio->setTitulo( 'Relatório do Birt' );
$obRelatorio->setVersaoBirt('2.5.0');
$obRelatorio->setExportaExcel(true);

if ($_REQUEST['inCodBemInicial'] != '') {
   $obRelatorio->addParametro('inCodBemInicial'   , $_REQUEST['inCodBemInicial']);
} else {
   $obRelatorio->addParametro('inCodBemInicial'   , '');
}

if ($_REQUEST['inCodBemFinal'] != '') {
   $obRelatorio->addParametro('inCodBemFinal'     , $_REQUEST['inCodBemFinal']);
} else {
   $obRelatorio->addParametro('inCodBemFinal'     , '');
}

if ($_REQUEST['stNumPlacaInicial'] != '') {
   $obRelatorio->addParametro('stNumPlacaInicial' , $_REQUEST['stNumPlacaInicial']);
} else {
   $obRelatorio->addParametro('stNumPlacaInicial' , '');
}

if ($_REQUEST['stNumPlacaFinal'] != '') {
   $obRelatorio->addParametro('stNumPlacaFinal'   , $_REQUEST['stNumPlacaFinal']);
} else {
   $obRelatorio->addParametro('stNumPlacaFinal'   , '');
}

if ($_REQUEST['stHdnDescricaoBem'] != '') {
   $obRelatorio->addParametro('stDescricaoBem'    , $_REQUEST['stHdnDescricaoBem']);
} else {
   $obRelatorio->addParametro('stDescricaoBem'    , '');
}

if ($_REQUEST['inCodEntidade'] != '') {
   $obRelatorio->addParametro('inCodEntidade'     , $_REQUEST['inCodEntidade']);
} else {
   $obRelatorio->addParametro('inCodEntidade'     , '');
}

if ($_REQUEST['inCGM'] != '') {
   $obRelatorio->addParametro('inCodFornecedor'   , $_REQUEST['inCGM']);
} else {
   $obRelatorio->addParametro('inCodFornecedor'   , '');
}

if ($_REQUEST['hdnUltimoOrgaoSelecionado'] != '') {
   $obRelatorio->addParametro('inCodOrgao'        , $_REQUEST['hdnUltimoOrgaoSelecionado']);
} else {
   $obRelatorio->addParametro('inCodOrgao'        , '');
}

if ($_REQUEST['inCodLocal'] != '') {
   $obRelatorio->addParametro('inCodLocal'        , $_REQUEST['inCodLocal']);
} else {
   $obRelatorio->addParametro('inCodLocal'        , '');
}

if ($_REQUEST['inCodNatureza'] != '') {
   $obRelatorio->addParametro('inCodNatureza'     , $_REQUEST['inCodNatureza']);
} else {
   $obRelatorio->addParametro('inCodNatureza'     , '');
}

if ($_REQUEST['inCodGrupo'] != '') {
   $obRelatorio->addParametro('inCodGrupo'        , $_REQUEST['inCodGrupo']);
} else {
   $obRelatorio->addParametro('inCodGrupo'        , '');
}

if ($_REQUEST['inCodEspecie'] != '') {
   $obRelatorio->addParametro('inCodEspecie'      , $_REQUEST['inCodEspecie']);
} else {
   $obRelatorio->addParametro('inCodEspecie'      , '');
}

if ($_REQUEST['inCodSituacao'] != '') {
   $obRelatorio->addParametro('inCodSituacao'     , $_REQUEST['inCodSituacao']);
} else {
   $obRelatorio->addParametro('inCodSituacao'     , '');
}

if ($_REQUEST['stDataInicialdtAquisicao'] != '') {
   $obRelatorio->addParametro('stDataInicialdtAquisicao' , $_REQUEST['stDataInicialdtAquisicao']);
} else {
   $obRelatorio->addParametro('stDataInicialdtAquisicao' , '');
}

if ($_REQUEST['stDataFinaldtAquisicao'] != '') {
   $obRelatorio->addParametro('stDataFinaldtAquisicao'   , $_REQUEST['stDataFinaldtAquisicao']);
} else {
   $obRelatorio->addParametro('stDataFinaldtAquisicao'   , '');
}

if ($_REQUEST['stDataInicialdtIncorporacao'] != '') {
   $obRelatorio->addParametro('stDataInicialIncorporacao'   , $_REQUEST['stDataInicialdtIncorporacao']);
} else {
   $obRelatorio->addParametro('stDataInicialIncorporacao'   , '');
}

if ($_REQUEST['stDataFinaldtIncorporacao'] != '') {
   $obRelatorio->addParametro('stDataFinalIncorporacao'   , $_REQUEST['stDataFinaldtIncorporacao']);
} else {
   $obRelatorio->addParametro('stDataFinalIncorporacao'   , '');
}

if (count($_REQUEST['atributos']) > 0) {
   $inCont = 1;
   foreach ($_REQUEST['atributos'] as $atributos) {
      if ($atributos !='') {
         $obRelatorio->addParametro('stAtributoDinamico'.(string) $inCont   , $atributos);
      } else {
         $obRelatorio->addParametro('stAtributoDinamico'.(string) $inCont   , '');
      }
      $inCont++;
      if ($inCont == 11) {
         break;
      }
   }
}

if (count($_REQUEST['atributos']) > 0) {
   $obRelatorio->addParametro('inCountAtributos', count($_REQUEST['atributos']));
} else {
   $obRelatorio->addParametro('inCountAtributos', 0 );
}

$obRAdministracaoConfiguracao = new RConfiguracaoConfiguracao;
$obRAdministracaoConfiguracao->setExercicio('2010');
$obRAdministracaoConfiguracao->setCodModulo(6);
$obRAdministracaoConfiguracao->setParametro('placa_alfanumerica');
$obRAdministracaoConfiguracao->consultar();

if ($obRAdministracaoConfiguracao->getValor() != '') {
   $obRelatorio->addParametro('stPlacaAlfanumerica'   , $obRAdministracaoConfiguracao->getValor());
} else {
   $obRelatorio->addParametro('stPlacaAlfanumerica'   , '');
}

# Parâmetros para determinar a visibilidade das colunas no relatório.
if ($_REQUEST['demo_cod_local'] != '') {
   $obRelatorio->addParametro('hidden_cod_local'       , $_REQUEST['demo_cod_local']);
} else {
   $obRelatorio->addParametro('hidden_cod_local'       , '');
}

if ($_REQUEST['demo_cod_bem'] =! '') {
   $obRelatorio->addParametro('hidden_cod_bem'         , $_REQUEST['demo_cod_bem']);
} else {
   $obRelatorio->addParametro('hidden_cod_bem'         , '');
}

if ($_REQUEST['demo_descricao'] != '') {
   $obRelatorio->addParametro('hidden_descricao'       , $_REQUEST['demo_descricao']);
} else {
   $obRelatorio->addParametro('hidden_descricao'       , '');
}

if ($_REQUEST['demo_dt_aquisicao'] != '') {
   $obRelatorio->addParametro('hidden_dt_aquisicao'    , $_REQUEST['demo_dt_aquisicao']);
} else {
   $obRelatorio->addParametro('hidden_dt_aquisicao'    , '');
}

if ($_REQUEST['demo_dt_incorporacao'] != '') {
   $obRelatorio->addParametro('hidden_dt_incorporacao' , $_REQUEST['demo_dt_incorporacao']);
} else {
   $obRelatorio->addParametro('hidden_dt_incorporacao' , '');
}

if ($_REQUEST['demo_placa'] != '') {
   $obRelatorio->addParametro('hidden_placa'           , $_REQUEST['demo_placa']);
} else {
   $obRelatorio->addParametro('hidden_placa'           , '');
}

if ($_REQUEST['demo_valor'] != '') {
   $obRelatorio->addParametro('hidden_valor'           , $_REQUEST['demo_valor']);
} else {
   $obRelatorio->addParametro('hidden_valor'           , '');
}

if ($_REQUEST['demo_nota_fiscal'] != '') {
   $obRelatorio->addParametro('hidden_nota_fiscal'     , $_REQUEST['demo_nota_fiscal']);
} else {
   $obRelatorio->addParametro('hidden_nota_fiscal'     , '');
}

if ($_REQUEST['demo_nom_cgm'] != '') {
   $obRelatorio->addParametro('hidden_nom_cgm'         , $_REQUEST['demo_nom_cgm']);
} else {
   $obRelatorio->addParametro('hidden_nom_cgm'         , '');
}

$stOrder ="";
if (count($_REQUEST['inCamposSelecionados']) >0) {
    $stOrder = implode(',',$_REQUEST['inCamposSelecionados']);
}

$obRelatorio->addParametro('order', $stOrder);

# Gera o Preview do relatório.
$obRelatorio->preview();

?>
