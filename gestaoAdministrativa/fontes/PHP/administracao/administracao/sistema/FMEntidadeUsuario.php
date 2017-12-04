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
 * Página de Formulário do Estagiário
 * Data de Criação: 14/06/2007

 * @author Analista: Diego Lemos de Souza
 * @author Desenvolvedor: Diego Lemos de Souza

 * @ignore

 * Casos de uso: uc-04.00.00

 $Id: FMEntidadeUsuario.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "EntidadeUsuario";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::remove('acao');

$stAcao = $request->get('acao');
$stCtrl = $request->get('stCtrl');
$inCodEntidade = $request->get('inCodEntidade');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnNivel =  new Hidden;
$obHdnNivel->setName   ( "inNivel" );
$obHdnNivel->setValue  ( $request->get("nivel") );

$obHdnGestao =  new Hidden;
$obHdnGestao->setName  ( "inCodGestao" );
$obHdnGestao->setValue ( $request->get("inCodGestao") );

$inCodEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura",8,Sessao::getExercicio());

$stTitulo = ($request->get("stTitulo", "") != "") ? $request->get("stTitulo") : $request->get("stNomeGestao");

$obHdnTitulo =  new Hidden;
$obHdnTitulo->setName  ( "stTitulo" );
$obHdnTitulo->setValue ( $stTitulo );

$obHdnVersao =  new Hidden;
$obHdnVersao->setName  ( "stVersao" );
$obHdnVersao->setValue ( $request->get("stVersao") );

$obLblUsuario = new Label();
$obLblUsuario->setRotulo("Usuário");
$obLblUsuario->setValue(Sessao::read('nomCgm'));

$obLblMensagem = new Label();
$obLblMensagem->setRotulo("Mensagem");

if ($inCodEntidadePrefeitura == 0) {
    $obLblMensagem->setValue("É necessário a configuração da entidade da prefeitura no caminho: Gestão Financeira :: Orçamento :: Configuração :: Alterar Configuração.");
} else {
    $obLblMensagem->setValue("Selecione a entidade para criação na Gestão de Recursos Humanos, se a entidade não possuir uma estrutura de banco de dados criada o sistema automaticamente irá criar esta estrutura. Para isso basta selecionar uma entidade e pressionar o botão OK.");
}

include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
$obTEntidade = new TEntidade();
$stFiltro  = " AND exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= " AND cod_entidade != ".$inCodEntidadePrefeitura;
$obTEntidade->recuperaEntidades($rsEntidades,$stFiltro);

$arEntidades = array();
$arTmp = array();
$inCount = 0;

while ( !$rsEntidades->eof() ) {
    $stFiltro = " WHERE nspname = 'pessoal_".$rsEntidades->getCampo('cod_entidade')."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro);

    //Administrador do sistema
    if ($rsEsquemas->getNumLinhas() == -1) {
        $arTmp[$inCount]['exercicio']        = $rsEntidades->getCampo('exercicio');
        $arTmp[$inCount]['cod_entidade']     = $rsEntidades->getCampo('cod_entidade');
        $arTmp[$inCount]['numcgm']           = $rsEntidades->getCampo('numcgm');
        $arTmp[$inCount]['cod_responsavel']  = $rsEntidades->getCampo('cod_responsavel');
        $arTmp[$inCount]['cod_resp_tecnico'] = $rsEntidades->getCampo('cod_resp_tecnico');
        $arTmp[$inCount]['cod_profissao']    = $rsEntidades->getCampo('cod_profissao');
        $arTmp[$inCount]['sequencia']        = $rsEntidades->getCampo('sequencia');
        $arTmp[$inCount]['nom_cgm']          = $rsEntidades->getCampo('nom_cgm');

        $inCount++;
    }
    $rsEntidades->proximo();
}

$rsEntidades = new RecordSet;
$rsEntidades->preenche( $arTmp );

$obCmbEntidade = new Select;
$obCmbEntidade->setRotulo  ( "Entidade" );
$obCmbEntidade->setTitle   ( "Selecione a entidade para trabalho." );
$obCmbEntidade->setName    ( "inCodEntidade" );
$obCmbEntidade->setValue   ( $inCodEntidade );
$obCmbEntidade->setStyle   ( "width: 400px" );
$obCmbEntidade->addOption  ( "", "Selecione" );
$obCmbEntidade->setNull(false);
$obCmbEntidade->setCampoId("cod_entidade");
$obCmbEntidade->setCampoDesc("nom_cgm");
$obCmbEntidade->preencheCombo($rsEntidades);
if ($inCodEntidadePrefeitura == 0) {
    $obCmbEntidade->setDisabled(true);
}

$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick("BloqueiaFrames(true,false);Salvar();");

if ($inCodEntidadePrefeitura == 0) {
    $obBtnOK->setDisabled(true);
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addTitulo("Entidade do Usuário");
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnNivel );
$obFormulario->addHidden ( $obHdnGestao );
$obFormulario->addHidden ( $obHdnTitulo );
$obFormulario->addHidden ( $obHdnVersao );

$obFormulario->addComponente($obLblUsuario);
$obFormulario->addComponente($obLblMensagem);
$obFormulario->addComponente($obCmbEntidade);
$obFormulario->defineBarra(array($obBtnOK));;
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
