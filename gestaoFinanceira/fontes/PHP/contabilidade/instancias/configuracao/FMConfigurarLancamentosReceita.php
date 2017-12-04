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
    * Página de Formulário Configurar Lançamentos de Receita
    * Data de Criação   : 21/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @ignore

    $Id: FMConfigurarLancamentosReceita.php 66481 2016-09-01 20:15:15Z michel $

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";

$stPrograma = "ConfigurarLancamentosReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$stFiltroEntidade = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao)." AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade = new TEntidade;
$obTEntidade->recuperaEntidades($rsEntidades, $stFiltroEntidade);

//não possui permissão de acesso a exercícios anteriores a 2012
if ( !Sessao::getExercicio() > '2012' ) {
    SistemaLegado::AlertaAviso( CAM_FW_INSTANCIAS."index/inicial.php", "Esta ação não está disponível à  exercícios anteriores a 2012!", "n_incluir", "erro" );
}

$obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;

$stOrdem = " ORDER BY pc.cod_estrutural ";

$stFiltroArrecadacaoDireta = " AND pc.exercicio = '".Sessao::getExercicio()."'
            AND  ( pc.cod_estrutural like '4.1.1.%'
                OR pc.cod_estrutural like '4.1.2.%'
                OR pc.cod_estrutural like '4.1.3.%'
                OR pc.cod_estrutural like '4.2.0.%'
                OR pc.cod_estrutural like '4.2.1.%'
                OR pc.cod_estrutural like '4.2.2.%'
                OR pc.cod_estrutural like '4.2.3.%'
                OR pc.cod_estrutural like '4.2.4.%'
                OR pc.cod_estrutural like '4.3.0.%'
                OR pc.cod_estrutural like '4.3.1.%'
                OR pc.cod_estrutural like '4.3.2.%'
                OR pc.cod_estrutural like '4.3.3.%'
                OR pc.cod_estrutural like '4.4.0.%'
                OR pc.cod_estrutural like '4.4.1.%'
                OR pc.cod_estrutural like '4.4.2.%'
                OR pc.cod_estrutural like '4.4.4.%'
                OR pc.cod_estrutural like '4.4.5.%' ) ";
//recupera contas empenho debito
$obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsArrecadacaoReceitaCredito, $stFiltroArrecadacaoDireta, $stOrdem);

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodReceitaLista = new Hidden;
$obHdnCodReceitaLista->setName( "codContaReceitaLista" );
$obHdnCodReceitaLista->setId( "codContaReceitaLista" );
$obHdnCodReceitaLista->setValue( "" );

$obHdnBoArrecadacao = new Hidden;
$obHdnBoArrecadacao->setName( "boArrecadacao" );
$obHdnBoArrecadacao->setId( "boArrecadacao" );
$obHdnBoArrecadacao->setValue( "FALSE" );

$obHdnCodContaCredito = new Hidden;
$obHdnCodContaCredito->setName( "inCodContaCredito" );
$obHdnCodContaCredito->setId( "inCodContaCredito" );
$obHdnCodContaCredito->setValue( "" );

$obSpnListaReceitaDisponiveis = new Span;
$obSpnListaReceitaDisponiveis->setId("listaReceitaDisponiveis");

############################################# ABA EMPENHO
$obRdoArrecadacaoDireta = new Radio;
$obRdoArrecadacaoDireta->setName( "rdoArrecadacao" );
$obRdoArrecadacaoDireta->setId( "arrecadacaoDireta" );
$obRdoArrecadacaoDireta->setValue( "arrecadacaoDireta" );
$obRdoArrecadacaoDireta->setLabel( "Arrecadação Direta" );
$obRdoArrecadacaoDireta->setRotulo( "Lançamentos" );
$obRdoArrecadacaoDireta->setChecked( true );
$obRdoArrecadacaoDireta->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamento', this.value)");

$obRdoOperacoesCredito = new Radio;
$obRdoOperacoesCredito->setName( "rdoArrecadacao" );
$obRdoOperacoesCredito->setId( "operacoesCredito" );
$obRdoOperacoesCredito->setValue( "operacoesCredito" );
$obRdoOperacoesCredito->setLabel( "Operações de Crédito" );
$obRdoOperacoesCredito->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamento', this.value)");

$obRdoAlienacaoBens = new Radio;
$obRdoAlienacaoBens->setName( "rdoArrecadacao" );
$obRdoAlienacaoBens->setId( "alienacaoBens" );
$obRdoAlienacaoBens->setValue( "alienacaoBens" );
$obRdoAlienacaoBens->setLabel( "Alienação Bens Móveis/Imóveis" );
$obRdoAlienacaoBens->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamento', this.value)");

$obRdoDividaAtiva = new Radio;
$obRdoDividaAtiva->setName( "rdoArrecadacao" );
$obRdoDividaAtiva->setId( "dividaAtiva" );
$obRdoDividaAtiva->setValue( "dividaAtiva" );
$obRdoDividaAtiva->setLabel( "Dívida Ativa" );
$obRdoDividaAtiva->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamento', this.value)");

$obCmbEmpenhoCredito = new Select;
$obCmbEmpenhoCredito->setName( "stLancamentoCreditoReceita" );
$obCmbEmpenhoCredito->setId( "stLancamentoCreditoReceita" );
$obCmbEmpenhoCredito->setRotulo( "Crédito" );
$obCmbEmpenhoCredito->addOption( "", "Selecione" );
$obCmbEmpenhoCredito->setValue( " " );
$obCmbEmpenhoCredito->setCampoId( "cod_conta" );
$obCmbEmpenhoCredito->setCampoDesc( "[cod_estrutural] - [nom_conta]" );
$obCmbEmpenhoCredito->preencheCombo( $rsArrecadacaoReceitaCredito );
$obCmbEmpenhoCredito->setStyle( "width: 520" );

//RECUPERA LISTA DESPESA
$obROrcamentoDespesa = new ROrcamentoReceita;
$obROrcamentoDespesa->listarReceitaConfiguracaoLancamento($rsLista);

//INSTÃNCIA DO OBJETO LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Estrutural");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "VINCULAR" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:selecionaReceita(this);" );
$obLista->ultimaAcao->setTipoLink( "checkbox" );
$obLista->ultimaAcao->addCampo("1","cod_conta"  );
$obLista->ultimaAcao->addCampo("2","descricao"  );
$obLista->commitAcao();
$obLista->montaInnerHtml();

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnCodReceitaLista );
$obFormulario->addHidden( $obHdnBoArrecadacao );
$obFormulario->addHidden( $obHdnCodContaCredito );
$obFormulario->addSpan( $obSpnListaReceitaDisponiveis );
$obFormulario->addTitulo( "Informações de Lançamento" );
$obFormulario->addTitulo( "Lançamentos de Receita" );
$obFormulario->addComponente( $obRdoArrecadacaoDireta );
$obFormulario->addComponente( $obRdoOperacoesCredito );
$obFormulario->addComponente( $obRdoAlienacaoBens );
$obFormulario->addComponente( $obRdoDividaAtiva );
$obFormulario->addTitulo( "Contas para Lançamento" );
$obFormulario->addComponente( $obCmbEmpenhoCredito );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';
SistemaLegado::executaFrameOculto("jQuery('#listaReceitaDisponiveis').html('".$obLista->getHtml()."') ;");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
