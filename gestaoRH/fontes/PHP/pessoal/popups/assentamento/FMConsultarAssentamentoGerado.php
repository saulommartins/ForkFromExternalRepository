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
    * Página de Formulário do Consultar Assentamento Gerado
    * Data de Criação: 13/12/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.41

    $Id: FMConsultarAssentamentoGerado.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarAssentamentoGerado";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php?".Sessao::getId();
$pgJS       = "JS".$stPrograma.".js";
//$jsOnload   = "executaFuncaoAjax('processarForm','&inRegistro=".$_REQUEST['inContrato']."&inCodMes=".$_REQUEST['inCodMes']."&inAno=".$_REQUEST['inAno']."');";
?>
 <script type="text/javascript">
 function zebra(id, classe)
 {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) == 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php
$stAcao      = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$obRPessoalContrato = new RPessoalContrato;
$obRPessoalContrato->listarCgmDoRegistro($rsContrato,$_REQUEST['inContrato']);
$stCGM = $rsContrato->getCampo('nom_cgm');

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorRegimeFuncao.class.php");
$obTPessoalContratoServidorRegimeFuncao = new TPessoalContratoServidorRegimeFuncao();
$stFiltro = " AND contrato_servidor_regime_funcao.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
$obTPessoalContratoServidorRegimeFuncao->recuperaRegimeDeContratos($rsRegime,$stFiltro);

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSubDivisaoFuncao.class.php");
$obTPessoalContratoServidorSubDivisaoFuncao = new TPessoalContratoServidorSubDivisaoFuncao();
$stFiltro = " AND contrato_servidor_sub_divisao_funcao.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
$obTPessoalContratoServidorSubDivisaoFuncao->recuperaSubDivisaoDeContratos($rsSubDivisao,$stFiltro);

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
$obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao();
$stFiltro = " AND contrato_servidor_funcao.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
$obTPessoalContratoServidorFuncao->recuperaDeContratos($rsFuncao,$stFiltro);

$stRegime = $rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao")."/".$rsFuncao->getCampo("descricao");

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNomeacaoPosse.class.php");
$obTPessoalContratoServidorNomeacaoPosse = new TPessoalContratoServidorNomeacaoPosse();
$stFiltro = " AND contrato_servidor_nomeacao_posse.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
$obTPessoalContratoServidorNomeacaoPosse->recuperaNomeacaoPosseDeContratos($rsNomeacaoPosse,$stFiltro);

$dtAdmissao = $rsNomeacaoPosse->getCampo("dt_admissao");

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php");
$obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado();

$stFiltro = "";
if (trim($_REQUEST["inCodTipoClassificacao"])!="") {
    $stFiltro .= " AND classificacao_assentamento.cod_tipo = ".$_REQUEST["inCodTipoClassificacao"]."                  \n";
}
if (trim($_REQUEST["stDataInicial"])!="" && trim($_REQUEST["stDataFinal"])!="") {
    $stFiltro .= " AND assentamento_gerado.periodo_inicial >= to_date('".$_REQUEST["stDataInicial"]."', 'dd/mm/yyyy') \n";
    $stFiltro .= " AND assentamento_gerado.periodo_final <= to_date('".$_REQUEST["stDataFinal"]."', 'dd/mm/yyyy')   \n";
}
$stFiltro .= " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
$stOrdem   = " ORDER BY pessoal.assentamento_gerado.periodo_inicial ";
$obTPessoalAssentamentoGerado->recuperaRelacionamento($rsAssentamentoGerado,$stFiltro, $stOrdem);

$stTabela  = "<center>";
$stTabela .= "<table id='processos' width=100%>";
$stTabela .= "<tr><td class=labelcentercabecalho align=left width=56%><font size=-1><b>Assentamento</b></font></td>
                  <td class=labelcentercabecalho align=left width=33%><font size=-1><b>Período</font></b></td>
                  <td class=labelcentercabecalho align=left width=10%><font size=-1><b>Quant. Dias</font></b></td></tr>";
while (!$rsAssentamentoGerado->eof()) {

    $stTabela .= "<tr><td class=show_dados align=left width=56%><font size=-1>".$rsAssentamentoGerado->getCampo("descricao_assentamento")."</font></td>
                      <td class=show_dados align=left width=33%><font size=-1>".$rsAssentamentoGerado->getCampo("periodo_inicial")." até ".$rsAssentamentoGerado->getCampo("periodo_final")."</td>
                      <td class=show_dados align=left width=10%><font size=-1>".(SistemaLegado::datediff('d', SistemaLegado::dataToSql($rsAssentamentoGerado->getCampo("periodo_inicial")),SistemaLegado::dataToSql($rsAssentamentoGerado->getCampo("periodo_final")))+1)."</font></td></tr>";
    $rsAssentamentoGerado->proximo();

}
$stTabela .= "</table>";
$stTabela .= "</center>";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obLblContrato = new Label;
$obLblContrato->setName                         ( "stContrato"                                                          );
$obLblContrato->setRotulo                       ( "Matrícula"                                                            );
$obLblContrato->setValue                        ( $_REQUEST['inContrato']." - ".$stCGM                                    );

$obLblRegime = new Label;
$obLblRegime->setName                              ( "stRegime"                                                               );
$obLblRegime->setRotulo                            ( "Regime/Subdivisão/Função"                                                                 );
$obLblRegime->setValue                             ( $stRegime                                                                );

$obLblAdmissao = new Label;
$obLblAdmissao->setName                              ( "stAdmissao"                                                               );
$obLblAdmissao->setRotulo                            ( "Admissão"                                                                 );
$obLblAdmissao->setValue                             ( $dtAdmissao                                                                );

$obSpnAssentamentosGerados = new Span;
$obSpnAssentamentosGerados->setId                 ( "spnAssentamentosGerados"                                               );
$obSpnAssentamentosGerados->setValue($stTabela);

$obBtnFechar = new Button;
$obBtnFechar->setName                    ( "btnFechar" );
$obBtnFechar->setValue                   ( "Fechar"    );
$obBtnFechar->setTipo                    ( "button"    );
$obBtnFechar->obEvento->setOnClick       ( "window.parent.window.close();"  );

$botoesForm = array ( $obBtnFechar );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo                        ( "Dados da Matrícula do Servidor"                                       );
$obFormulario->addComponente                    ( $obLblContrato                                                        );
$obFormulario->addComponente                    ( $obLblRegime                                                             );
$obFormulario->addComponente                    ( $obLblAdmissao );
$obFormulario->addTitulo                        ( "Dados do Assentamento Gerado"                                       );
$obFormulario->addSpan                          ( $obSpnAssentamentosGerados                                              );
$obFormulario->defineBarra ( $botoesForm );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
        <script>zebra('processos','zb');</script>
