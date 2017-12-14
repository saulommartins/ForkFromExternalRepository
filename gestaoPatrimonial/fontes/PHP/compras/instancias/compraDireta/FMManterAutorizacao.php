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
 * Página de Formulário para gerar autorizacao de empenho na compra direta
 * Data de Criação   : 29/01/2007

 * @author Analista: Gelson
 * @author Desenvolvedor: Lucas Teixeira Stephanou

 * @ignore

 * Casos de uso : uc-03.04.32

 $Id: FMManterAutorizacao.php 63367 2015-08-20 21:27:34Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
require_once TCOM."TComprasCompraDireta.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

include_once($pgJS);

Sessao::write( 'arAutorizacao'  , array() );
Sessao::write( 'assinaturas'    , array() );

$boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', '35', Sessao::getExercicio());
$boReservaRigida = ($boReservaRigida == 'true') ? true : false;

$boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
$boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

if(!$boReservaRigida && !$boReservaAutorizacao){
    $stMsg = "Obrigatório Configurar o Tipo de Reserva em: Gestão Patrimonial :: Compras :: Configuração :: Alterar Configuração";
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$_REQUEST["stAcao"], $stMsg, "unica", "aviso", Sessao::getId(), "../");
}

$obTCompraDireta = new TComprasCompraDireta();
$obTCompraDireta->setDado( 'exercicio'          , Sessao::getExercicio()            );
$obTCompraDireta->setDado( 'cod_compra_direta'  , $_REQUEST['inCodCompraDireta']    );
$obTCompraDireta->setDado( 'cod_entidade'       , $_REQUEST['inCodEntidade']        );
$obTCompraDireta->setDado( 'cod_modalidade'     , $_REQUEST['inCodModalidade']      );
$obTCompraDireta->setDado( 'exercicio_entidade' , $_REQUEST['stExercicioEntidade']  );
$obTCompraDireta->recuperaCompraDiretaAutorizacaoEmpenho( $rsCompraDireta );

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

//Define o Hidde de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obHdnCompraDireta = new Hidden;
$obHdnCompraDireta->setName  ('inCodCompraDireta');
$obHdnCompraDireta->setValue ( $rsCompraDireta->getCampo('cod_compra_direta') );

$obHdnModalidade = new Hidden;
$obHdnModalidade->setName  ( 'inCodModalidade' );
$obHdnModalidade->setValue ( $rsCompraDireta->getCampo('cod_modalidade') );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setId    ( 'inCodEntidade' );
$obHdnEntidade->setName  ( 'inCodEntidade' );
$obHdnEntidade->setValue ( $rsCompraDireta->getCampo('cod_entidade') );

$obHdnExercicioEntidade = new Hidden;
$obHdnExercicioEntidade->setName  ( 'stExercicioEntidade');
$obHdnExercicioEntidade->setValue ( $rsCompraDireta->getCampo('exercicio_entidade') );

$obHdnDtCompraDireta = new Hidden;
$obHdnDtCompraDireta->setName  ( 'stDtCompraDireta' );
$obHdnDtCompraDireta->setValue ( $rsCompraDireta->getCampo('data') );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo ( 'Entidade' );
$obLblEntidade->setValue  ( $rsCompraDireta->getCampo('cod_entidade').' - '.$rsCompraDireta->getCampo('entidade') );

$obLblModalidade = new Label;
$obLblModalidade->setRotulo ( 'Modalidade' );
$obLblModalidade->setValue  ( $rsCompraDireta->getCampo('cod_modalidade').' - '.$rsCompraDireta->getCampo('modalidade') );

$obLblCompraDireta = new Label;
$obLblCompraDireta->setRotulo ( 'Código Compra Direta' );
$obLblCompraDireta->setValue  ( $rsCompraDireta->getCampo('cod_compra_direta') );

$obLblTipoObjeto = new Label;
$obLblTipoObjeto->setRotulo ( 'Tipo Objeto' );
$obLblTipoObjeto->setValue  ( $rsCompraDireta->getCampo('cod_tipo_objeto' ).' - '.$rsCompraDireta->getCampo('tipo_objeto') );

$obLblObjeto = new Label;
$obLblObjeto->setRotulo ( 'Objeto' );
$obLblObjeto->setValue  ( $rsCompraDireta->getCampo('objeto') );

$obLblDtCompraDireta = new Label;
$obLblDtCompraDireta->setRotulo ( 'Data da Compra Direta' );
$obLblDtCompraDireta->setValue  ( $rsCompraDireta->getCampo('data') );

$obLblDataEntrega = new Label;
$obLblDataEntrega->setRotulo ( 'Data de Entrega da Proposta' );
$obLblDataEntrega->setValue  ( $rsCompraDireta->getCampo('dt_entrega') );

$obLblDataValidade = new Label;
$obLblDataValidade->setRotulo ( 'Validade da Proposta' );
$obLblDataValidade->setValue  ( $rsCompraDireta->getCampo('dt_entrega') );

$obLblCondicoes = new Label;
$obLblCondicoes->setRotulo ( 'Condições de Pagamento' );
$obLblCondicoes->setValue  ( $rsCompraDireta->getCampo('condicoes_pagamento') );

$obLblPrazoEntrega = new Label;
$obLblPrazoEntrega->setRotulo ( 'Prazo de Entregas' );
$obLblPrazoEntrega->setValue  ( $rsCompraDireta->getCampo('prazo_entrega').' dias' );

$obLblMapaCompras = new Label;
$obLblMapaCompras->setRotulo ( 'Número do Mapa de Compras' );
$obLblMapaCompras->setValue  ( $rsCompraDireta->getCampo('cod_mapa').'/'.$rsCompraDireta->getCampo('exercicio_mapa') );

$obLblTotalMapa = new Label;
$obLblTotalMapa->setId     ( 'stTotalMapa' );
$obLblTotalMapa->setName   ( 'stTotalMapa' );
$obLblTotalMapa->setRotulo ( 'Total do Mapa' );

# Componente que monta as Assinaturas.
$obMontaAssinaturas = new IMontaAssinaturas(null, 'autorizacao_empenho');
$obMontaAssinaturas->definePapeisDisponiveis('autorizacao_empenho');
$obMontaAssinaturas->setOpcaoAssinaturas( false );

$obSpnLabels = new Span;
$obSpnLabels->setId ( 'spnLabels' );

$obSpnAutorizacoes = new Span;
$obSpnAutorizacoes->setId ( 'spnAutorizacoes' );

$obSpnItens = new Span;
$obSpnItens->setId ( 'spnItens' );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addTitulo ( "Gerar Autorização de Empenho" );

# Componentes Hidden.
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnCompraDireta      );
$obFormulario->addHidden( $obHdnEntidade          );
$obFormulario->addHidden( $obHdnExercicioEntidade );
$obFormulario->addHidden( $obHdnDtCompraDireta    );
$obFormulario->addHidden( $obHdnModalidade        );

# Componentes Diversos.
$obFormulario->addComponente ( $obLblEntidade       );
$obFormulario->addComponente ( $obLblModalidade     );
$obFormulario->addComponente ( $obLblCompraDireta   );
$obFormulario->addComponente ( $obLblDtCompraDireta );
$obFormulario->addComponente ( $obLblTipoObjeto     );
$obFormulario->addComponente ( $obLblObjeto         );
$obFormulario->addComponente ( $obLblDataEntrega    );
$obFormulario->addComponente ( $obLblDataValidade   );
$obFormulario->addComponente ( $obLblCondicoes      );
$obFormulario->addComponente ( $obLblPrazoEntrega   );
$obFormulario->addComponente ( $obLblMapaCompras    );
$obFormulario->addComponente ( $obLblTotalMapa      );

# Monta o componente de Assinaturas.
$obMontaAssinaturas->geraFormulario( $obFormulario );

# Componentes Span.
$obFormulario->addSpan ( $obSpnLabels );
$obFormulario->addSpan ( $obSpnAutorizacoes );
$obFormulario->addSpan ( $obSpnItens );

$obFormulario->Cancelar ($pgList, true);
$obFormulario->show();

if ($obMontaAssinaturas->getOpcaoAssinaturas()) {
    echo $obMontaAssinaturas->disparaLista();
}

# Parâmetros necessários para requisitar as informações da Compra Direta.
$stParams .= "&inCodCompraDireta=".$rsCompraDireta->getCampo('cod_compra_direta');
$stParams .= "&inCodEntidade=".$rsCompraDireta->getCampo('cod_entidade');
$stParams .= "&inCodModalidade=".$rsCompraDireta->getCampo('cod_modalidade');
$stParams .= "&inCodMapaCompras=".$rsCompraDireta->getCampo('cod_mapa');
$stParams .= "&stExercicioMapaCompras=".$rsCompraDireta->getCampo('exercicio_mapa');
$stParams .= "&stExercicioEntidade=".$rsCompraDireta->getCampo('exercicio_entidade');
$stParams .= "&boAlteraAnula=true";

# Carrega as informações básicas da Compra Direta.
$stJs  = "<script type='text/javascript'> \n";
$stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId().$stParams."','buscaInfoCompraDireta'); \n";
$stJs .= "</script> \n";

echo $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
