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

    * Página de Formulário de Consulta de compras direta
    * Data de Criação   : 28/01/2009

    * @author Analista: Gelson W
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    $Id: FMManterConsultaCompraDireta.php 37723 2009-01-28 17:07:26Z luiz $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCompraDireta.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCompraDireta";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stFiltroPg = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltroPg = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltroPg .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltroPg .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}
$arCompraDireta = array();
$inCodHomologada= Sessao::read('inCodHomologada');

$stFiltro  = " where compra_direta.cod_compra_direta=" . $_REQUEST['inCodCompraDireta']  ;
$stFiltro .= " and compra_direta.cod_entidade = " . $_REQUEST['inCodEntidade']  ;
$stFiltro .= " and compra_direta.cod_modalidade =" . $_REQUEST['inCodModalidade']  ;
$stFiltro .= " and compra_direta.exercicio_entidade = '". Sessao::getExercicio() ."'";

//buscar informacoes da compra direta
require_once(TCOM . "TComprasCompraDireta.class.php");

$obTCompraDireta = new TComprasCompraDireta();

//consulta com mesmo filtro montado acima
$obTCompraDireta->recuperaTodos ( $rsCompraDireta , $stFiltro);

//formata valores e inserir em array
$arCompraDireta = array();
$arCompraDireta['cod_compra'] = $rsCompraDireta->getCampo('cod_compra_direta');
$arCompraDireta['cod_entidade'] = $rsCompraDireta->getCampo('cod_entidade');
$arCompraDireta['exercicio_entidade'] = $rsCompraDireta->getCampo('exercicio_entidade');
$arCompraDireta['cod_modalidade'] = $rsCompraDireta->getCampo('cod_modalidade');
$arCompraDireta['cod_objeto'] = $rsCompraDireta->getCampo('cod_objeto');
$arCompraDireta['cod_tipo_objeto'] = $rsCompraDireta->getCampo('cod_tipo_objeto');
$arCompraDireta['exercicio_mapa'] = $rsCompraDireta->getCampo('exercicio_mapa');
$arCompraDireta['cod_mapa'] = $rsCompraDireta->getCampo('cod_mapa');

list($ano, $mes, $dia) = explode("-", substr($rsCompraDireta->getCampo('timestamp'), 0, 10));
$stDtCompraDireta = $dia."/".$mes."/".$ano;

$arCompraDireta['obDtCompraDireta'] = $stDtCompraDireta;

list ( $ano, $mes, $dia ) = explode('-' , $rsCompraDireta->getCampo('dt_entrega_proposta') );
$arCompraDireta['dt_entrega_proposta'] = $dia.$mes.$ano;

list ( $ano, $mes, $dia ) = explode('-' , $rsCompraDireta->getCampo('dt_validade_proposta') );
$arCompraDireta['dt_validade_proposta'] = $dia.$mes.$ano;

//$arCompraDireta['condicoes_pagamento'] = urldecode($rsCompraDireta->getCampo('condicoes_pagamento'));
$arCompraDireta['condicoes_pagamento'] = $rsCompraDireta->getCampo('condicoes_pagamento');
$arCompraDireta['prazo_entrega'] = $rsCompraDireta->getCampo('prazo_entrega');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//pegar entidade
$inNumCgmEntidade = SistemaLegado::pegaDado("numcgm", "orcamento.entidade","where cod_entidade = " . $arCompraDireta['cod_entidade']);
$stEntidade  = $arCompraDireta['cod_entidade'] . " - " . SistemaLegado::pegaDado("nom_cgm", "sw_cgm","where numcgm = " . $inNumCgmEntidade);
$obLblEntidade = new Label();
$obLblEntidade->setRotulo( "Entidade" );
$obLblEntidade->setValue( $stEntidade );

$stModalidade = SistemaLegado::pegaDado('descricao', 'compras.modalidade','where cod_modalidade = ' . $arCompraDireta['cod_modalidade']);
$obLblModalidade = new Label();
$obLblModalidade->setRotulo( "Modalidade" );
$obLblModalidade->setValue( $stModalidade );

$obLblCompraDireta= new Label();
$obLblCompraDireta->setRotulo( "Compra Direta" );
$obLblCompraDireta->setValue( $arCompraDireta['cod_compra'] );

$obLblTipoObjeto= new Label();
$obLblTipoObjeto->setRotulo( "Tipo Objeto" );
$obLblTipoObjeto->setValue( $arCompraDireta['cod_tipo_objeto'] . ' - ' . SistemaLegado::pegaDado('descricao' , 'compras.tipo_objeto' ,'where cod_tipo_objeto = ' . $arCompraDireta['cod_tipo_objeto'] ) );

$obLblObjeto= new Label();
$obLblObjeto->setRotulo( "Objeto" );
$obLblObjeto->setName( 'lblObjeto' );
$obLblObjeto->setId( 'lblObjeto' );
$obLblObjeto->setValue( $arCompraDireta['cod_objeto'] . ' - ' . stripslashes(SistemaLegado::pegaDado('descricao' , 'compras.objeto' ,'where cod_objeto = ' . $arCompraDireta['cod_objeto'] ) ) );

$obLblDataEntregaProposta= new Label();
$obLblDataEntregaProposta->setRotulo( "Data de Entrega da Proposta" );
$obLblDataEntregaProposta->setValue( $arCompraDireta['dt_entrega_proposta'] );

$obLblDataValidadeProposta= new Label();
$obLblDataValidadeProposta->setRotulo( "Validade da Proposta" );
$obLblDataValidadeProposta->setValue( $arCompraDireta['dt_validade_proposta'] );

$obLblCondicoesPagamento= new Label();
$obLblCondicoesPagamento->setRotulo( "Condições de Pagamento" );
$obLblCondicoesPagamento->setValue( $arCompraDireta['condicoes_pagamento'] );

$obLblPrazoEntrega= new Label();
$obLblPrazoEntrega->setRotulo( "Prazo de Entrega" );
$obLblPrazoEntrega->setValue( $arCompraDireta['prazo_entrega'] . " dia(s)");

$obLblMapaCompras= new Label();
$obLblMapaCompras->setRotulo( "Mapa de Compras" );
$obLblMapaCompras->setValue( $arCompraDireta['cod_mapa'] . "/" . $arCompraDireta['exercicio_mapa'] );

$obHdnObjeto = new Hidden();
$obHdnObjeto->setName('hdnObjeto');
$obHdnObjeto->setId('hdnObjeto');
$obHdnObjeto->setValue( $arCompraDireta['cod_objeto'] );

$obLblDia = new Label();
$obLblDia->setRotulo( " &nbsp; Dias" );
$obLblDia->setValue (" &nbsp; Dias"  );

$obTxtTotalMapa = new Label();
$obTxtTotalMapa->setId( "stTotalMapa" );
$obTxtTotalMapa->setName( "stTotalMapa" );
$obTxtTotalMapa->setRotulo( "Total do Mapa" );

$obLblDtCompraDireta = new Label();
$obLblDtCompraDireta->setId    ( "obLblDtCompraDireta" );
$obLblDtCompraDireta->setName  ( "obLblDtCompraDireta" );
$obLblDtCompraDireta->setRotulo( "Data da Compra Direta" );
$obLblDtCompraDireta->setValue ( $arCompraDireta['obDtCompraDireta'] );

$obTComprasCompraDireta = new TComprasCompraDireta();
$obTComprasCompraDireta->setDado( 'cod_compra_direta'      , $arCompraDireta["cod_compra"]);
$obTComprasCompraDireta->setDado( 'cod_entidade'      , $arCompraDireta["cod_entidade"]);
$obTComprasCompraDireta->setDado( 'cod_modalidade' , $arCompraDireta["cod_modalidade"]);
$obTComprasCompraDireta->setDado( 'cod_mapa' , $arCompraDireta["cod_mapa"]);
$obTComprasCompraDireta->setDado( 'exercicio_mapa' , $arCompraDireta['exercicio_mapa']);
$obTComprasCompraDireta->recuperaCompraDiretaAutorizacao($rsAturoizacaoEmpenho);

$obLblDataHomogacao = new Label;
$obLblDataHomogacao->setName  ('stDataHomologacao');
$obLblDataHomogacao->setRotulo('Data da Homologação');
$obLblDataHomogacao->setValue ($rsAturoizacaoEmpenho->getCampo('dt_homologacao'));

Sessao::write("arCompraDireta", $arCompraDireta );
Sessao::write("dt_homologacao", $_REQUEST["stDtHomologacao"] );

$obLnknRelatorio = new Link;
$obLnknRelatorio->setRotulo ("Dados da Autorização" );
$obLnknRelatorio->setValue  ("Relatório"       );
$obLnknRelatorio->setTarget ("oculto"           );
$obLnknRelatorio->setHref   (  CAM_GP_COM_INSTANCIAS."compraDireta/OCDadosCompraDireta.php");    

$obTComprasCompraDiretaStatus = new TComprasCompraDireta();
$obTComprasCompraDiretaStatus->setDado( 'cod_compra_direta'      , $arCompraDireta["cod_compra"]);
$obTComprasCompraDiretaStatus->setDado( 'cod_entidade'      , $arCompraDireta["cod_entidade"]);
$obTComprasCompraDiretaStatus->setDado( 'cod_modalidade' , $arCompraDireta["cod_modalidade"]);
$obTComprasCompraDiretaStatus->setDado( 'cod_mapa' , $arCompraDireta["cod_mapa"]);
$obTComprasCompraDiretaStatus->setDado( 'exercicio_mapa' , $arCompraDireta['exercicio_mapa']);
$obTComprasCompraDiretaStatus->recuperaStatusCompraDireta($rsStatus);

$obLblSituacao = new Label;
$obLblSituacao->setName  ('stSituacao');
$obLblSituacao->setRotulo('Situação');
$obLblSituacao->setValue ($rsStatus->getCampo('status'));

$obSpnItens = new Span;
$obSpnItens->setId( 'spnItens' );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltroPg;
$obBtnVoltar = new Button;
$obBtnVoltar->setName  ( "Voltar" );
$obBtnVoltar->setValue ( "Voltar" );
$obBtnVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addTitulo ( "Compra Direta" );

$obFormulario->addComponente ( $obLblEntidade );
$obFormulario->addComponente ( $obLblCompraDireta );
$obFormulario->addComponente ( $obLblMapaCompras	 );
$obFormulario->addComponente ( $obTxtTotalMapa );

$obFormulario->addComponente ( $obLblDtCompraDireta );
$obFormulario->addComponente ( $obLblModalidade );

$obFormulario->addComponente ( $obLblTipoObjeto	 );
$obFormulario->addComponente ( $obLblObjeto	 );
$obFormulario->addHidden 	 ( $obHdnObjeto );
$obFormulario->addComponente ( $obLblDataEntregaProposta	 );
$obFormulario->addComponente ( $obLblDataValidadeProposta	 );
$obFormulario->addComponente ( $obLblCondicoesPagamento	 );
$obFormulario->addComponente ( $obLblPrazoEntrega	 );

if(Sessao::read('inCodHomologada') != 3 && $rsAturoizacaoEmpenho->getCampo('dt_homologacao') != '' && $rsAturoizacaoEmpenho->getCampo('homologado') == 't'){
    $obFormulario->addComponente ( $obLblDataHomogacao);
    if($rsAturoizacaoEmpenho->getCampo('cod_autorizacao') != ''){
         $obFormulario->addComponente ( $obLnknRelatorio);
    }
   
} 
$obFormulario->addComponente ( $obLblSituacao);
$obFormulario->addSpan   ( $obSpnItens );

$obFormulario->defineBarra( array( $obBtnVoltar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

echo "<script type='text/javascript'>\n";
echo "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade=".$arCompraDireta['cod_entidade']."&stMapaCompras=".$arCompraDireta['cod_mapa']."/".$arCompraDireta['exercicio_mapa']."&boAlteraAnula=true','montaItensAlterar');";
echo "</script>\n";

?>
