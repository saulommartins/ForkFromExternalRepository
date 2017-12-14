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
/*
    * Lista para cadastro de compensações de horas
    * Data de Criação   : 14/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

switch ($_GET["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " AND cod_contrato = ".$_GET["inCodigo"];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsContratos,$stFiltro);
        break;
    case "lotacao":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
        $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
        $stFiltro = " WHERE contrato_servidor_orgao.cod_orgao = ".$_GET["inCodigo"];
        $stFiltro .= "   AND EXISTS (SELECT 1
                                        FROM ponto.compensacao_horas
                                        WHERE contrato_servidor_orgao.cod_contrato = compensacao_horas.cod_contrato
                                        AND compensacao_horas.dt_falta = to_date('".$_GET["dtFalta"]."','dd/mm/yyyy')
                                        AND compensacao_horas.dt_compensacao = to_date('".$_GET["dtCompensacao"]."','dd/mm/yyyy'))";
        $obTPessoalContratoServidorOrgao->recuperaContratosDaLotacao($rsContratos,$stFiltro," order by nom_cgm");
        break;
    case "local":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
        $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
        $stFiltro = " WHERE contrato_servidor_local.cod_local = ".$_GET["inCodigo"];
        $stFiltro .= "   AND EXISTS (SELECT 1
                                        FROM ponto.compensacao_horas
                                        WHERE contrato_servidor_local.cod_contrato = compensacao_horas.cod_contrato
                                        AND compensacao_horas.dt_falta = to_date('".$_GET["dtFalta"]."','dd/mm/yyyy')
                                        AND compensacao_horas.dt_compensacao = to_date('".$_GET["dtCompensacao"]."','dd/mm/yyyy'))";
        $obTPessoalContratoServidorLocal->recuperaContratosDoLocal($rsContratos,$stFiltro," order by nom_cgm");
        break;
    case "reg_sub_fun_esp":
        $arCodigo = explode('_',$_GET["inCodigo"]);
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
        $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao();
        $stFiltro  = " WHERE contrato_servidor_funcao.cod_cargo = ".$arCodigo[2];
        $stFiltro .= "   AND contrato_servidor_regime_funcao.cod_regime = ".$arCodigo[0];
        $stFiltro .= "   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = ".$arCodigo[1];
        if ($arCodigo[3] != 0) {
            $stFiltro .= " AND contrato_servidor_especialiade_funcao.cod_especialidade = ".$arCodigo[3];
        }
        $stFiltro .= "   AND EXISTS (SELECT 1
                                        FROM ponto.compensacao_horas
                                        WHERE contrato_servidor_funcao.cod_contrato = compensacao_horas.cod_contrato
                                        AND compensacao_horas.dt_falta = to_date('".$_GET["dtFalta"]."','dd/mm/yyyy')
                                        AND compensacao_horas.dt_compensacao = to_date('".$_GET["dtCompensacao"]."','dd/mm/yyyy'))";
        $obTPessoalContratoServidorFuncao->recuperaContratosDaFuncao($rsContratos,$stFiltro," order by nom_cgm");
        break;
}

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                                   );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obLista = new Lista;
$obLista->setRecordset( $rsContratos );
$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
