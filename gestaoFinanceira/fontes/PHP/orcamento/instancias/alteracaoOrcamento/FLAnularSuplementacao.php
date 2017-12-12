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
    * Página de Filtro de Anulação de Suplementações
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: melo $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.9  2007/05/21 18:54:32  melo
Bug #9229#

Revision 1.8  2006/07/28 17:39:25  leandro.zis
Bug #6689#

Revision 1.7  2006/07/24 20:19:42  andre.almeida
Bug #6408#

Revision 1.6  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AnularSuplementacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    Sessao::remove('filtro');
    Sessao::remove('pg');
    Sessao::remove('pos');
    Sessao::remove('paginando');
    Sessao::remove('link');
    //sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );
    //sessao->link = array();

    $obRegra = new ROrcamentoSuplementacao;
    $obRegra->addDespesaReducao();
    $obRegra->setExercicio( Sessao::getExercicio() );
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade, "cod_entidade" );
    $obRegra->listarTipo( $rsTipoSuplementacao );

    // Exclui tipo 16 do recordset ( Anulação )
    $inCount = 0;
    $arAuxiliar = array();
    foreach ( $rsTipoSuplementacao->getElementos() as $arTipoSuplementacao ) {
        if ($arTipoSuplementacao['cod_tipo'] != 16) {
            $arAuxiliar[$inCount]['cod_tipo']            = $arTipoSuplementacao['cod_tipo'];
            $arAuxiliar[$inCount]['exercicio']           = $arTipoSuplementacao['exercicio'];
            $arAuxiliar[$inCount]['nom_tipo']            = $arTipoSuplementacao['nom_tipo'];
            $arAuxiliar[$inCount]['lancamento_contabil'] = $arAuxiliar['lancamento_contabil'];
            $inCount++;
        }
    }

    $rsTipoSuplementacao = new RecordSet;
    $rsTipoSuplementacao->preenche( $arAuxiliar );

    //****************************************//
    //Define COMPONENTES DO FORMULARIO
    //****************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget( "telaPrincipal" );

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( $stCtrl );

    // Define Objeto TextBox para Codigo da Entidade
    $obTxtCodEntidade = new TextBox;
    $obTxtCodEntidade->setRotulo  ( "Entidade"             );
    $obTxtCodEntidade->setTitle   ( "Selecione a entidade." );
    $obTxtCodEntidade->setName    ( "inCodEntidade"        );
    if ($rsEntidade->getNumLinhas()==1) {
         $obTxtCodEntidade->setValue          ( $rsEntidade->getCampo('cod_entidade')  );
    } else $obTxtCodEntidade->setValue   ( $inCodEntidade         );
    $obTxtCodEntidade->setNull    ( false                  );
    $obTxtCodEntidade->setInteiro ( true                   );

    // Define objeto Select para Codigo da Entidade
    $obCmbCodEntidade = new Select;
    $obCmbCodEntidade->setRotulo    ( "Entidade"         );
    $obCmbCodEntidade->setTitle     ( "Selecione a entidade." );
    $obCmbCodEntidade->setName      ( "stNomEntidade"    );
    $obCmbCodEntidade->setValue     ( $inCodEntidade     );
    $obCmbCodEntidade->setCampoId   ( "cod_entidade"     );
    $obCmbCodEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
    if ($rsEntidade->getNumLinhas()>1) {
        $obCmbCodEntidade->addOption              ( "", "Selecione"               );
    }
    $obCmbCodEntidade->preencheCombo( $rsEntidade        );
    $obCmbCodEntidade->setNull      ( false              );

    // Define Objeto TextBox para Codigo do tipo de suplementação
    $obTxtCodTipoSuplementacao = new TextBox;
    $obTxtCodTipoSuplementacao->setRotulo  ( "Tipo de Suplementação"             );
    $obTxtCodTipoSuplementacao->setTitle   ( "Selecione o tipo de suplementação." );
    $obTxtCodTipoSuplementacao->setName    ( "inCodTipo"                         );
    $obTxtCodTipoSuplementacao->setValue   ( $inCodTipo                          );
    $obTxtCodTipoSuplementacao->setNull    ( true                                );
    $obTxtCodTipoSuplementacao->setInteiro ( true                                );

    // Define objeto Select para Codigo do tipo de suplementação
    $obCmbCodTipoSuplementacao = new Select;
    $obCmbCodTipoSuplementacao->setRotulo    ( "Tipo de Suplementação"             );
    $obCmbCodTipoSuplementacao->setTitle     ( "Selecione o tipo de suplementação." );
    $obCmbCodTipoSuplementacao->setName      ( "stNomTipo"                         );
    $obCmbCodTipoSuplementacao->setValue     ( $inCodTipo                          );
    $obCmbCodTipoSuplementacao->setCampoId   ( "cod_tipo"                          );
    $obCmbCodTipoSuplementacao->setCampoDesc ( "[cod_tipo] - [nom_tipo]"           );
    $obCmbCodTipoSuplementacao->addOption    ( "", "Selecione"                     );
    $obCmbCodTipoSuplementacao->preencheCombo( $rsTipoSuplementacao                );
    $obCmbCodTipoSuplementacao->setNull      ( true                                );

    // Define Objeto BuscaInner para Norma
    $obBscNorma = new BuscaInner;
    $obBscNorma->setRotulo ( "Lei/Decreto"   );
    $obBscNorma->setTitle  ( "Selecione uma lei ou decreto." );
    $obBscNorma->setNulL   ( true                     );
    $obBscNorma->setId     ( "stNomTipoNorma"         );
    $obBscNorma->setValue  ( $stNomTipoNorma          );
    $obBscNorma->obCampoCod->setName     ( "inCodNorma" );
    $obBscNorma->obCampoCod->setId       ( "inCodNorma" );
    $obBscNorma->obCampoCod->setSize     ( 10           );
    $obBscNorma->obCampoCod->setMaxLength( 7            );
    $obBscNorma->obCampoCod->setValue    ( $inCodNorma  );
    $obBscNorma->obCampoCod->setAlign    ( "left"       );
    $obBscNorma->obCampoCod->obEvento->setOnChange("SistemaLegado::BloqueiaFrames(true,false);buscaDado('buscaNorma');");
    $obBscNorma->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stNomTipoNorma','','".Sessao::getId()."','800','550');");

    // Define objeto Data para data da suplementação
    $obDtSuplemetacao = new Data;
    $obDtSuplemetacao->setName   ( "stDtSuplementacao"                );
    $obDtSuplemetacao->setRotulo ( "Data da Suplementação"            );
    $obDtSuplemetacao->setTitle  ( 'Informe a data da lei ou decreto.');
    $obDtSuplemetacao->setNull   ( true                               );

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->setAjuda ( "UC-02.01.07"           );

    $obFormulario->addHidden( $obHdnAcao              );
    $obFormulario->addHidden( $obHdnCtrl              );

    $obFormulario->addTitulo( "Dados para Filtro"  );
    $obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbCodEntidade );
    $obFormulario->addComponenteComposto( $obTxtCodTipoSuplementacao, $obCmbCodTipoSuplementacao );
    $obFormulario->addComponente( $obBscNorma );
    $obFormulario->addComponente( $obDtSuplemetacao );

    $obFormulario->OK();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
