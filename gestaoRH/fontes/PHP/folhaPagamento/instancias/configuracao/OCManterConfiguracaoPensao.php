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
* Página processamento oculto de Controle de Pensão Alimenticia
* Data de: Criação   : 03/04/2006
# 20060419

* @author Analista: Vandré Miguel Ramos.
* @author Desenvolvedor: Bruce Cruz de Sena

* @ignore

* Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"                                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php'                               );
include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPensaoEvento.class.php'                         );
include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPensaoFuncaoPadrao.class.php'                   );

function buscaFuncao($boExecuta = false, $stCodFuncao = '')
{
    $stJs = '';

    if ($stCodFuncao) {
        $arCodFuncao = explode('.', $stCodFuncao );
    } else {
        if ($_POST['inCodFuncao']) {
            $arCodFuncao = explode('.',$_POST['inCodFuncao'])  ;
        }
    }

    if ($arCodFuncao) {
       $obRFuncao = new RFuncao;
       $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
       $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
       $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
       $obRFuncao->consultar();
       $stNomeFuncao = $obRFuncao->getNomeFuncao();
       if ( !empty($stNomeFuncao) ) {
           $stJs .= "d.getElementById('stFuncao').innerHTML = '".$stNomeFuncao."';\n";
           if ($stCodFuncao) {
               $stJs .= "f.inCodFuncao.value = '$stCodFuncao';\n";
           }
       } else {
           $stJs .= "f.inCodFuncao.value = '';\n";
           $stJs .= "f.inCodFuncao.focus();\n";
           $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '&nbsp;';\n";
           $stJs .= "alertaAviso('@Função informada não existe. (".$_POST['inCodFuncao'].")','form','erro','".Sessao::getId()."');";
       }
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}//function buscaFuncao(

function preencherEvento($boExecuta=false)
{
    global $_POST, $_GET;
    $obTFOLEvento = new TFolhaPagamentoEvento;
    $inCodTipo = $_GET['inCodTipo'];
    $stNatureza= $_GET['stNatureza'];
    $obTFOLEvento->recuperaEventoCodigoNatureza( $rsEvento, $_POST['stInner_Cod_'.$inCodTipo] , $stNatureza, true );

    $stInner = "stInner_".$inCodTipo;
    if ( $rsEvento->getNumLinhas() > 0 ) {
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsEvento->getCampo('descricao')."';  \n";
    } else {
        $stJs .= "f.stInner_Cod_".$inCodTipo.".value = '';                  \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

/*
Função....: preencherInnerEventos
Objetivo..: Procurar eventos de configuração de pensão e preencher os buscainner
Data......: 30/05/206

*/
function preencherInnerEventos($boExecuta=false)
{
    $stJs = '';
    $obTFOLPensaoEventos = new TFolhaPagamentoPensaoEvento;
    $obTFOLPensaoEventos->recuperaPensaoEventoEventos( $rsEventos );

    while (!$rsEventos->eof()) {
        $stInner  = 'stInner_'.$rsEventos->getCampo('cod_tipo');
        $stCod    = 'stInner_Cod_'.$rsEventos->getCampo('cod_tipo');
        $stJs    .= "d.getElementById('".$stInner."').innerHTML = '".$rsEventos->getCampo('descricao')."';  \n";
        $stJs    .= "f.".$stCod  .".value = '".$rsEventos->getCampo('codigo').   "';  \n";
        $rsEventos->proximo();
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencheFuncao($boExecuta = false)
{
    $obTFOLPensaoFuncao = new TFolhaPagamentoPensaoFuncaoPadrao;
    $obTFOLPensaoFuncao->recuperaUltimaPensaoFuncaoPadrao( $rsFuncao );

    if ( !$rsFuncao->eof() ) {
      $stCodFuncao = $rsFuncao->getCampo('cod_modulo')     .'.'.
                     $rsFuncao->getCampo('cod_biblioteca') .'.'.
                     $rsFuncao->getCampo('cod_funcao');
    }
    $stJs = buscaFuncao(false,  $stCodFuncao );

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}//function buscaFuncao(

switch ($_POST["stCtrl"]) {
    case 'buscaFuncao':
          buscaFuncao ( true );
    break;

    case 'preencherEvento':
          preencherEvento ( true );
    break;

}

?>
