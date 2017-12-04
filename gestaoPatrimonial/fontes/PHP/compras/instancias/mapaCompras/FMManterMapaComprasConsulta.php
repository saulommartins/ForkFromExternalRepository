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
 * Página de Inclusão de Mapa de Compras
 * Data de Criação   : 19/09/2006

 * @author Analista: Cleisson Barbosa
 * @author Desenvolvedor: Anderson C. Konze

 * @ignore

 * Casos de uso: uc-03.04.05

 $Id: FMManterMapaComprasConsulta.php 63099 2015-07-24 18:30:55Z franver $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_COMPONENTES."IMontaSolicitacao.class.php";
include_once CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapa.class.php";
include_once CAM_GP_LIC_COMPONENTES."ISelectTipoLicitacao.class.php";

$stPrograma = "ManterMapaCompras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgGera = "OCGera".$stPrograma.".php";

include_once $pgJs;
include_once $pgOcul;

Sessao::write('solicitacoes' , array());
Sessao::write('itens' , array());
Sessao::write('solicitacoes_excluidas' , array());
Sessao::write('solicitacoes_anuladas' , array());
Sessao::write('ultimoCodigo' , 0);

$inCodMapa   = $_REQUEST['cod_mapa'];
$stExercicio = $_REQUEST['exercicio'];

# Busca as informações do Mapa de Compras.
$obTComprasMapa = new TComprasMapa;
$stFiltro  = " WHERE  mapa.cod_mapa  = ".$inCodMapa;
$stFiltro .= "   AND  mapa.exercicio = '".$stExercicio."'";
$obTComprasMapa->recuperaRelacionamento($rsMapa, $stFiltro);

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( 'stAcao'   );
$obHdnAcao->setValue ( 'imprimir' );

$obHdnCodMapa = new Hidden;
$obHdnCodMapa->setName  ( 'inCodMapa' );
$obHdnCodMapa->setValue ( $inCodMapa  );

$obHdnExercicioMapa = new Hidden;
$obHdnExercicioMapa->setName  ( 'stExercicioMapa' );
$obHdnExercicioMapa->setValue ( $stExercicio  );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName  ('stDataMapa');
$obHdnTimestamp->setValue ($rsMapa->getCampo('dt_mapa'));

$obLblExercicio = new Label;
$obLblExercicio->setRotulo ('Exercício'   );
$obLblExercicio->setName   ('stExercicio' );
$obLblExercicio->setValue  ( $stExercicio );

$obTxtCodMapa = new Label;
$obTxtCodMapa->setRotulo ( 'Número do Mapa' );
$obTxtCodMapa->setValue  ( $inCodMapa       );

$obLblTipoLicitacao = new Label;
$obLblTipoLicitacao->setRotulo ( 'Tipo de Cotação' );
$obLblTipoLicitacao->setName   ( 'stTipoLicitacao'   );
$obLblTipoLicitacao->setValue  ( $rsMapa->getCampo('cod_tipo_licitacao').' - '.$rsMapa->getCampo('descricao_tipo_licitacao'));

$obLblTipoRegistroPrecos = new Label;
$obLblTipoRegistroPrecos->setRotulo( 'Registro de Preços' );
$obLblTipoRegistroPrecos->setName  ( 'stTipoRegistroPrecos' );
$obLblTipoRegistroPrecos->setId    ( 'stTipoRegistroPrecos' );

// Objetos utilizados para emitir o Mapa de Compras.
$obCheckBoxEmitirMapa = new CheckBox;
$obCheckBoxEmitirMapa->setName  ('boEmitirMapa');
$obCheckBoxEmitirMapa->setId    ('boEmitirMapa');
$obCheckBoxEmitirMapa->setRotulo('Emitir Mapa de Compras');
$obCheckBoxEmitirMapa->setTitle ('Emitir Mapa de Compras');
$obCheckBoxEmitirMapa->setValue ('true');
$obCheckBoxEmitirMapa->obEvento->setOnClick("if (this.checked) { jQuery('#btnOk').removeAttr('disabled'); } else { jQuery('#btnOk').attr('disabled', 'disabled'); }");

$obRadioMostraDadoSim = new Radio;
$obRadioMostraDadoSim->setRotulo ('Exibir Valores Monetários');
$obRadioMostraDadoSim->setTitle  ('Marque sim para que o mapa imprima os valores monetários como Vlr. Unitário, Vlr. Última Compra ou marque não para emitir esses campos em branco.');
$obRadioMostraDadoSim->setLabel  ('Sim');
$obRadioMostraDadoSim->setName   ('boMostraDado');
$obRadioMostraDadoSim->setId     ('boMostraDado');
$obRadioMostraDadoSim->setValue  ('true');
$obRadioMostraDadoSim->setChecked(true);

$obRadioMostraDadoNao = new Radio;
$obRadioMostraDadoNao->setLabel  ('Não');
$obRadioMostraDadoNao->setName   ('boMostraDado');
$obRadioMostraDadoNao->setId     ('boMostraDado');
$obRadioMostraDadoNao->setValue  ('false');

$obSpnSolicitacoes = new Span;
$obSpnSolicitacoes->setId ('spnSolicitacoes');

$obSpnItens = new Span;
$obSpnItens->setId('spnItens');

$obSpnItem = new Span;
$obSpnItem->setId('spnItem');

$obForm = new Form;
$obForm->setAction ( $pgGera  );
$obForm->setId     ( "frm"  );

//Define formulário com abas
$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda( 'UC-03.04.05' );

$obFormulario->addHidden     ( $obHdnAcao            );
$obFormulario->addHidden     ( $obHdnCodMapa         );
$obFormulario->addHidden     ( $obHdnExercicioMapa   );
$obFormulario->addHidden     ( $obHdnTimestamp       );
$obFormulario->addAba        ( "Mapa"                );
$obFormulario->addTitulo     ( "Dados do Mapa"       );
$obFormulario->addComponente ( $obLblExercicio       );
$obFormulario->addComponente ( $obTxtCodMapa         );
$obFormulario->addComponente ( $obLblTipoLicitacao   );
$obFormulario->addComponente ( $obLblTipoRegistroPrecos );
$obFormulario->addTitulo     ( "Impressão"           );
$obFormulario->addComponente ( $obCheckBoxEmitirMapa );
$obFormulario->addComponenteComposto($obRadioMostraDadoSim, $obRadioMostraDadoNao);
$obFormulario->addSpan       ( $obSpnSolicitacoes    );

include_once 'FMManterMapaComprasAbaTotais.php';

# Aba dos Itens
$obFormulario->addAba    ( "Itens"     );
$obFormulario->addSpan   ( $obSpnItens );
$obFormulario->addSpan   ( $obSpnItem  );

# Aba de Totais por item
$obFormulario->addAba    ( "Totais"     );
$obFormulario->addSpan   ( $obSpnTotais );

# Define Objeto Button para voltar a listagem
$obBtnListagemSolicitacao = new Button;
$obBtnListagemSolicitacao->setValue( "Voltar" );
$obBtnListagemSolicitacao->obEvento->setOnClick("javascript:location.href='".$pgList."?".Sessao::getId()."&stAcao=".$stAcao."'");

# Define objeto para gerar o Mapa de Compra.
$obButtonEmitirMapa = new Button;
$obButtonEmitirMapa->setId       ('btnOk');
$obButtonEmitirMapa->setValue    ('Ok');
$obButtonEmitirMapa->obEvento->setOnClick("Salvar();");

$obFormulario->defineBarra ( array($obButtonEmitirMapa, $obBtnListagemSolicitacao),"left","" );

$obFormulario->show();

montaMapa($inCodMapa, $stExercicio);

$stJs  = montaListaSolicitacoes($rsMapa->getCampo('cod_tipo_licitacao'), $stAcao);
$stJs .= montaListaItens($rsRecordSet, $rsMapa->getCampo('cod_tipo_licitacao'), $stAcao);
$stJs .= preencheRegistroPrecos($inCodMapa, $stExercicio);

SistemaLegado::executaFrameOculto($stJs);


?>
