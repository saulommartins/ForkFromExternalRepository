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
    * Página processamento ocuto Pessoal ServidorP
    * Data de Criação   : 14/12/2004
    *

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: tiago $
    $Date: 2007-05-30 09:49:57 -0300 (Qua, 30 Mai 2007) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                                 );

//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOculIdentificacao = "OC".$stPrograma."AbaIdentificacao.php";
$pgOculDocumentacao  = "OC".$stPrograma."AbaDocumentacao.php";
$pgOculContrato      = "OC".$stPrograma."AbaContrato.php";
$pgOculPrevidencia   = "OC".$stPrograma."AbaPrevidencia.php";
$pgOculDependentes   = "OC".$stPrograma."AbaDependentes.php";
$pgOculAtributos     = "OC".$stPrograma."AbaAtributos.php";
$pgJS                = "JS".$stPrograma.".js";

include_once ($pgOculDependentes);

function gerarSpanPrevidencia($boExecuta=false)
{
    $rsLista = new RecordSet;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoPrevidencia->obTPrevidencia->setDado('cod_contrato',$_REQUEST['inCodContrato']);
    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoPrevidencia->obTPrevidencia->setDado("cod_vinculo", Sessao::read("inCodVinculo"));
    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoPrevidencia->listarPrevidencia( $rsLista );
    $obLista        = new Lista;
    $arPrevidencias = array();

    while ( !$rsLista->eof() ) {
        if ( $rsLista->getCampo('booleano') == 'true' ) {
            $arPrevidencias[] = $rsLista->getCampo('cod_previdencia');
            //$stJs .= " d.getElementById('inCodAbaPrevidencia_".$rsLista->getCampo('tipo_previdencia')."_".$rsLista->getCampo('cod_previdencia')."_".$i."').checked = true; ";
        }
        $rsLista->proximo();
        $i++;
    }

    Sessao::write("PREVIDENCIA",$arPrevidencias);
    $rsLista->setPrimeiroElemento();

    $obLista->setRecordSet( $rsLista );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obChkPrevidencia = new CheckBox;
    $obChkPrevidencia->setName  ( "inCodAbaPrevidencia_[tipo_previdencia]_[cod_previdencia]_"  );
    $obChkPrevidencia->setId    ( "inCodAbaPrevidencia_[tipo_previdencia]_[cod_previdencia]_"  );
    $obChkPrevidencia->setValue ( "true");
    $obChkPrevidencia->obEvento->setOnClick( "buscaValor('validaPrevidencia',4)" );

    $obLista->addDadoComponente( $obChkPrevidencia );
    $obLista->ultimoDado->setCampo( "[booleano]" );
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->commitDadoComponente();

    $obHdnPrevidencia = new Hidden;
    $obHdnPrevidencia->setName           ( "stTipoAbaPrevidencia"   );
    $obHdnPrevidencia->setValue          ( 'tipo_previdencia' );

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "cod_previdencia" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "tipo_previdencia" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnPrevidencia').innerHTML = '".$stHtml."'; \n";

    //Percorre lista para marcar como checked os campos selecionados na lista de itens
    $rsLista->setPrimeiroElemento();
    $i = 1;
    while ( !$rsLista->eof() ) {
        if ( $rsLista->getCampo('booleano') == 'true' ) {
            $stJs .= " d.getElementById('inCodAbaPrevidencia_".$rsLista->getCampo('tipo_previdencia')."_".$rsLista->getCampo('cod_previdencia')."_".$i."').checked = true; \n";
        }
        $rsLista->proximo();
        $i++;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function validaPrevidencia()
{
    $arPrevidencia = array();
    $stTipo = array();
    $inNumLinhasPrevidencia = '0';
    foreach ($_POST as $campo => $value) {
        $chkCampo = explode("_",$campo);
        if ($chkCampo[0] == "inCodAbaPrevidencia") {
            $inNumLinhasPrevidencia = $chkCampo[2];
            if ( is_array( $stTipo ) ) {
                if ( in_array( $chkCampo[1],  $stTipo )) {
                    $stMensagem = "Somente duas previdências com tipos diferentes são permitidos para cadastro.";
                    $stJs .= "alertaAviso('".$stMensagem."', 'form', 'erro', '".Sessao::getId()."');";
                    $stJs .= "f.". $campo .".checked    = false;";
                } else {
                    $stTipo[] = $chkCampo[1];
                    $arPrevidencia[] = $chkCampo[2];
                }
            } else {
                $stTipo[] = $chkCampo[1];
                $arPrevidencia[] = $chkCampo[2];
            }
        }
    }
    Sessao::write('PREVIDENCIA',$arPrevidencia);
    $stJs .= validaDataLimiteSalarioFamilia();

    return $stJs;
}

switch ($_POST["stCtrl"]) {
    case "gerarSpanPrevidencia":
        $stJs .= gerarSpanPrevidencia();
    break;
    case "validaPrevidencia":
        $stJs .= validaPrevidencia();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
