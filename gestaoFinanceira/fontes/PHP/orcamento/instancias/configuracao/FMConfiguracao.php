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
    * Interface de Alteração das configuração do orçamento
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    $Id: FMConfiguracao.php 64548 2016-03-11 18:28:10Z evandro $

    * Casos de uso: uc-02.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE . 'validaGF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoConfiguracao.class.php';
include_once CAM_GA_CSE_NEGOCIO . 'RProfissao.class.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';
include_once CAM_GF_EMP_MAPEAMENTO . 'TEmpenhoEmpenho.class.php';
include_once CAM_GF_CONT_NEGOCIO . 'RContabilidadeLancamento.class.php';

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "Configuracao";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRProfissao             = new RProfissao;

$obREntidadeOrcamento     = new ROrcamentoEntidade;
$obREntidadeOrcamento->setExercicio( Sessao::getExercicio()    );
$obREntidadeOrcamento->obRCGM->setNumCGM( Sessao::read('numCgm')  );
$obREntidadeOrcamento->listarUsuariosEntidade( $rsEntidade );

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio( Sessao::getExercicio()    );
$obRConfiguracaoOrcamento->consultarConfiguracao();

// Máscaras
                         $obRConfiguracaoOrcamento->setDedutora (false );
$stMascReceita         = $obRConfiguracaoOrcamento->recuperaMascaraReceita();
                         $obRConfiguracaoOrcamento->setDedutora (true );
$stMascReceitaDedutora = $obRConfiguracaoOrcamento->recuperaMascaraReceita();
$stMascPosicaoDespesa  = $obRConfiguracaoOrcamento->getMascClassDespesa();
$stMascDespesa         = $obRConfiguracaoOrcamento->getMascDespesa();

$stAcao = "alterarConfiguracao";

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction      ( $pgProc );
$obForm->setTarget      ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( "" );

$obHdnAcaoModulo = new Hidden;
$obHdnAcaoModulo->setName     ( "acao" );
$obHdnAcaoModulo->setValue    ( $_GET['acao'] );

$obHdnModulo = new Hidden;
$obHdnModulo->setName     ( "inCodModulo" );
$obHdnModulo->setValue    ( $obRConfiguracaoOrcamento->getCodModulo() );

$obRContabilidadeLancamento  = new RContabilidadeLancamento;
$obRContabilidadeLancamento->obRContabilidadeLote->setExercicio(Sessao::getExercicio());
$obErro = $obRContabilidadeLancamento->listar( $rsLancamento, "", $boTransacao="");

if ($rsLancamento->getNumLinhas() > 0) {
    $boExiste = true;
} else {
    $boExiste = false;
}

$obTxtCodigoEntidadePrefeitura = new TextBox;
$obTxtCodigoEntidadePrefeitura->setName        ( "inCodEntidadePrefeitura"   );
$obTxtCodigoEntidadePrefeitura->setId          ( "inCodEntidadePrefeitura"   );
$obTxtCodigoEntidadePrefeitura->setValue       ( $obRConfiguracaoOrcamento->getCodEntidadePrefeitura()    );
$obTxtCodigoEntidadePrefeitura->setRotulo      ( "Entidade Prefeitura"          );
$obTxtCodigoEntidadePrefeitura->setTitle       ( "Selecione a entidade."         );
$obTxtCodigoEntidadePrefeitura->obEvento->setOnChange( "limparCampos();"        );
$obTxtCodigoEntidadePrefeitura->setInteiro     ( true                           );
$obTxtCodigoEntidadePrefeitura->setNull        ( true                          );
$obTxtCodigoEntidadePrefeitura->setReadOnly    ( $obRConfiguracaoOrcamento->getCodEntidadePrefeitura() ? $boExiste : false );

$obCmbNomeEntidadePrefeitura = new Select;
$obCmbNomeEntidadePrefeitura->setName          ( "stNomeEntidadePrefeitura"     );
$obCmbNomeEntidadePrefeitura->setId            ( "stNomeEntidadePrefeitura"     );
$obCmbNomeEntidadePrefeitura->setValue         ( $obRConfiguracaoOrcamento->getCodEntidadePrefeitura()       );
$obCmbNomeEntidadePrefeitura->addOption        ( "", "Selecione"                );
$obCmbNomeEntidadePrefeitura->obEvento->setOnChange( "limparCampos();"          );
$obCmbNomeEntidadePrefeitura->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidadePrefeitura->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidadePrefeitura->setStyle         ( "width: 520"                   );
$obCmbNomeEntidadePrefeitura->preencheCombo    ( $rsEntidade                    );
$obCmbNomeEntidadePrefeitura->setNull          ( true                           );
$obCmbNomeEntidadePrefeitura->setDisabled      ( $obRConfiguracaoOrcamento->getCodEntidadePrefeitura() ? $boExiste : false );

$obTxtCodigoEntidadeCamara = new TextBox;
$obTxtCodigoEntidadeCamara->setName        ( "inCodEntidadeCamara"          );
$obTxtCodigoEntidadeCamara->setId          ( "inCodEntidadeCamara"          );
$obTxtCodigoEntidadeCamara->setValue       ( $obRConfiguracaoOrcamento->getCodEntidadeCamara()           );
$obTxtCodigoEntidadeCamara->setRotulo      ( "Entidade Câmara"              );
$obTxtCodigoEntidadeCamara->setTitle       ( "Selecione a entidade."         );
$obTxtCodigoEntidadeCamara->obEvento->setOnChange( "limparCampos();"         );
$obTxtCodigoEntidadeCamara->setInteiro     ( true                            );
$obTxtCodigoEntidadeCamara->setNull        ( true                            );
$obTxtCodigoEntidadeCamara->setReadOnly    ( $obRConfiguracaoOrcamento->getCodEntidadeCamara() ? $boExiste : false );

$obCmbNomeEntidadeCamara = new Select;
$obCmbNomeEntidadeCamara->setName          ( "stNomeEntidadeCamara"         );
$obCmbNomeEntidadeCamara->setId            ( "stNomeEntidadeCamara"         );
$obCmbNomeEntidadeCamara->setValue         ( $obRConfiguracaoOrcamento->getCodEntidadeCamara()           );
$obCmbNomeEntidadeCamara->addOption        ( "", "Selecione"                );
$obCmbNomeEntidadeCamara->obEvento->setOnChange( "limparCampos();"          );
$obCmbNomeEntidadeCamara->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidadeCamara->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidadeCamara->setStyle         ( "width: 520"                   );
$obCmbNomeEntidadeCamara->preencheCombo    ( $rsEntidade                    );
$obCmbNomeEntidadeCamara->setNull          ( true                           );
$obCmbNomeEntidadeCamara->setdisabled      ( $obRConfiguracaoOrcamento->getCodEntidadeCamara() ? $boExiste : false );

$obTxtCodigoEntidadeRPPS = new TextBox;
$obTxtCodigoEntidadeRPPS->setName        ( "inCodEntidadeRPPS"          );
$obTxtCodigoEntidadeRPPS->setId          ( "inCodEntidadeRPPS"          );
$obTxtCodigoEntidadeRPPS->setValue       ( $obRConfiguracaoOrcamento->getCodEntidadeRPPS()           );
$obTxtCodigoEntidadeRPPS->setRotulo      ( "Entidade RPPS"              );
$obTxtCodigoEntidadeRPPS->setTitle       ( "Selecione a entidade."         );
$obTxtCodigoEntidadeRPPS->obEvento->setOnChange( "limparCampos();"        );
$obTxtCodigoEntidadeRPPS->setInteiro     ( true                           );
$obTxtCodigoEntidadeRPPS->setNull        ( true                          );
$obTxtCodigoEntidadeRPPS->setReadOnly    ( $obRConfiguracaoOrcamento->getCodEntidadeRPPS() ? $boExiste : false );

$obCmbNomeEntidadeRPPS = new Select;
$obCmbNomeEntidadeRPPS->setName          ( "stNomeEntidadeRPPS"         );
$obCmbNomeEntidadeRPPS->setId            ( "stNomeEntidadeRPPS"         );
$obCmbNomeEntidadeRPPS->setValue         ( $obRConfiguracaoOrcamento->getCodEntidadeRPPS()           );
$obCmbNomeEntidadeRPPS->addOption        ( "", "Selecione"                );
$obCmbNomeEntidadeRPPS->obEvento->setOnChange( "limparCampos();"          );
$obCmbNomeEntidadeRPPS->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidadeRPPS->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidadeRPPS->setStyle         ( "width: 520"                   );
$obCmbNomeEntidadeRPPS->preencheCombo    ( $rsEntidade                    );
$obCmbNomeEntidadeRPPS->setNull          ( true                           );
$obCmbNomeEntidadeRPPS->setDisabled      ( $obRConfiguracaoOrcamento->getCodEntidadeRPPS() ? $boExiste : false );

$obTxtCodigoEntidadeConsorcio = new TextBox;
$obTxtCodigoEntidadeConsorcio->setName        ( "inCodEntidadeConsorcio"          );
$obTxtCodigoEntidadeConsorcio->setId          ( "inCodEntidadeConsorcio"          );
$obTxtCodigoEntidadeConsorcio->setValue       ( $obRConfiguracaoOrcamento->getCodEntidadeConsorcio()           );
$obTxtCodigoEntidadeConsorcio->setRotulo      ( "Entidade Consórcio"              );
$obTxtCodigoEntidadeConsorcio->setTitle       ( "Selecione a entidade."         );
$obTxtCodigoEntidadeConsorcio->obEvento->setOnChange( "limparCampos();"        );
$obTxtCodigoEntidadeConsorcio->setInteiro     ( true                           );
$obTxtCodigoEntidadeConsorcio->setNull        ( true                          );

$obCmbNomeEntidadeConsorcio = new Select;
$obCmbNomeEntidadeConsorcio->setName          ( "stNomeEntidadeConsorcio"         );
$obCmbNomeEntidadeConsorcio->setId            ( "stNomeEntidadeConsorcio"         );
$obCmbNomeEntidadeConsorcio->setValue         ( $obRConfiguracaoOrcamento->getCodEntidadeConsorcio()           );
$obCmbNomeEntidadeConsorcio->addOption        ( "", "Selecione"                );
$obCmbNomeEntidadeConsorcio->obEvento->setOnChange( "limparCampos();"          );
$obCmbNomeEntidadeConsorcio->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidadeConsorcio->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidadeConsorcio->setStyle         ( "width: 520"                   );
$obCmbNomeEntidadeConsorcio->preencheCombo    ( $rsEntidade                    );
$obCmbNomeEntidadeConsorcio->setNull          ( true                           );

$obTEmpenhoEmpenho = new TEmpenhoEmpenho;
$obErro = $obTEmpenhoEmpenho->verificaEmpenhoRealizado($rsEmpenhos); // Se já existe empenho na base para o exercicio atual.
if (!$obErro->ocorreu() ) {
    if ($rsEmpenhos->getCampo("empenhos") >= 1) {

        $obLblFormaExecucao = new Label;
        $obLblFormaExecucao->setRotulo ("Forma de Execução do Orçamento");

        $inForma = $obRConfiguracaoOrcamento->getFormaExecucaoOrcamento();
            if ( $inForma == 1 ) $stForma = "Detalhado na Execução";
            if ( $inForma == 0 ) $stForma = "Detalhado no Orçamento";
        $obLblFormaExecucao->setValue ( $inForma." - ".$stForma );

        $obHdnForma = new Hidden;
        $obHdnForma->setName ('inCodFormaExecucao');
        $obHdnForma->setValue( $inForma );
    } else {

        $obCmbCodFormaExecucao = new TextBox;
        $obCmbCodFormaExecucao->setName      ( "inCodFormaExecucao" );
        $obCmbCodFormaExecucao->setValue     ( $obRConfiguracaoOrcamento->getFormaExecucaoOrcamento() );
        $obCmbCodFormaExecucao->setRotulo    ( "Forma de Execução do Orçamento" );
        $obCmbCodFormaExecucao->setSize      ( "5" );
        $obCmbCodFormaExecucao->setMaxLength ( "1" );
        $obCmbCodFormaExecucao->setNull      ( true );
        $obCmbCodFormaExecucao->setInteiro   ( true  );

        $obCmbFormaExecucao = new Select;
        $obCmbFormaExecucao->setName      ( "stNomFormaExecucao" );
        $obCmbFormaExecucao->setValue     ( $obRConfiguracaoOrcamento->getFormaExecucaoOrcamento() );
        $obCmbFormaExecucao->setRotulo    ( "Forma de Execução do Orçamento" );
        $obCmbFormaExecucao->setTitle     ( "Selecione a forma de execução do orçamento." );
        $obCmbFormaExecucao->setStyle     ( "width: 250px" );
        $obCmbFormaExecucao->addOption    ( "", "Selecione" );
        $obCmbFormaExecucao->addOption    ( "0", "Detalhado no Orçamento" );
        $obCmbFormaExecucao->addOption    ( "1", "Detalhado na Execução"  );
        $obCmbFormaExecucao->setNull      ( true );
    }
} else {
    exit($obErro->getDescricao());
}

if (Sessao::getExercicio() < '2014') {
    $obCmbCodApuracaoMetas = new TextBox;
    $obCmbCodApuracaoMetas->setName      ( "inCodApuracaoMetas" );
    $obCmbCodApuracaoMetas->setValue     ( $obRConfiguracaoOrcamento->getUnidadeMedidaMetas() );
    $obCmbCodApuracaoMetas->setRotulo    ( "Período de Apuração das Metas" );
    $obCmbCodApuracaoMetas->setTitle     ( "Selecione o período de apuração das metas." );
    $obCmbCodApuracaoMetas->setSize      ( "5"  );
    $obCmbCodApuracaoMetas->setMaxLength ( "1"  );
    $obCmbCodApuracaoMetas->setNull      ( true );
    $obCmbCodApuracaoMetas->setInteiro   ( true );

    $obCmbApuracaoMetas = new Select;
    $obCmbApuracaoMetas->setName      ( "stNomApuracaoMetas" );
    $obCmbApuracaoMetas->setValue     ( $obRConfiguracaoOrcamento->getUnidadeMedidaMetas() );
    $obCmbApuracaoMetas->setRotulo    ( "Período de Apuração das Metas" );
    $obCmbApuracaoMetas->setStyle     ( "width: 250px"       );
    $obCmbApuracaoMetas->addOption    ( "", "Selecione"      );
    $obCmbApuracaoMetas->addOption    ( "1", "Mensal"        );
    $obCmbApuracaoMetas->addOption    ( "2", "Bimestral"     );
    $obCmbApuracaoMetas->addOption    ( "3", "Trimestral"    );
    $obCmbApuracaoMetas->addOption    ( "4", "Quadrimestral" );
    $obCmbApuracaoMetas->addOption    ( "6", "Semestral"     );
    $obCmbApuracaoMetas->setNull      ( false );
} else {
    //Unidade de Medida Metas Receita
    $obCmbCodApuracaoMetasReceita = new TextBox;
    $obCmbCodApuracaoMetasReceita->setName      ( "inCodApuracaoMetasReceita" );
    $obCmbCodApuracaoMetasReceita->setValue     ( $obRConfiguracaoOrcamento->getUnidadeMedidaMetasReceita() );
    $obCmbCodApuracaoMetasReceita->setRotulo    ( "Período de Apuração das Metas Receita" );
    $obCmbCodApuracaoMetasReceita->setTitle     ( "Selecione o período de apuração das metas de receita." );
    $obCmbCodApuracaoMetasReceita->setSize      ( "5"  );
    $obCmbCodApuracaoMetasReceita->setMaxLength ( "1"  );
    $obCmbCodApuracaoMetasReceita->setNull      ( true );
    $obCmbCodApuracaoMetasReceita->setInteiro   ( true );

    $obCmbApuracaoMetasReceita = new Select;
    $obCmbApuracaoMetasReceita->setName      ( "stNomApuracaoMetasReceita" );
    $obCmbApuracaoMetasReceita->setValue     ( $obRConfiguracaoOrcamento->getUnidadeMedidaMetasReceita() );
    $obCmbApuracaoMetasReceita->setRotulo    ( "Período de Apuração das Metas Receita" );
    $obCmbApuracaoMetasReceita->setStyle     ( "width: 250px"       );
    $obCmbApuracaoMetasReceita->addOption    ( "", "Selecione"      );
    $obCmbApuracaoMetasReceita->addOption    ( "1", "Mensal"        );
    $obCmbApuracaoMetasReceita->addOption    ( "2", "Bimestral"     );
    $obCmbApuracaoMetasReceita->addOption    ( "3", "Trimestral"    );
    $obCmbApuracaoMetasReceita->addOption    ( "4", "Quadrimestral" );
    $obCmbApuracaoMetasReceita->addOption    ( "6", "Semestral"     );
    $obCmbApuracaoMetasReceita->setNull      ( false );

    //Unidade de Medida Metas Despesa
    $obCmbCodApuracaoMetasDespesa = new TextBox;
    $obCmbCodApuracaoMetasDespesa->setName      ( "inCodApuracaoMetasDespesa" );
    $obCmbCodApuracaoMetasDespesa->setValue     ( $obRConfiguracaoOrcamento->getUnidadeMedidaMetasDespesa() );
    $obCmbCodApuracaoMetasDespesa->setRotulo    ( "Período de Apuração das Metas Despesa" );
    $obCmbCodApuracaoMetasDespesa->setTitle     ( "Selecione o período de apuração das metas despesa." );
    $obCmbCodApuracaoMetasDespesa->setSize      ( "5"  );
    $obCmbCodApuracaoMetasDespesa->setMaxLength ( "1"  );
    $obCmbCodApuracaoMetasDespesa->setNull      ( true );
    $obCmbCodApuracaoMetasDespesa->setInteiro   ( true );

    $obCmbApuracaoMetasDespesa = new Select;
    $obCmbApuracaoMetasDespesa->setName      ( "stNomApuracaoMetasDespesa" );
    $obCmbApuracaoMetasDespesa->setValue     ( $obRConfiguracaoOrcamento->getUnidadeMedidaMetasDespesa() );
    $obCmbApuracaoMetasDespesa->setRotulo    ( "Período de Apuração das Metas Despesa" );
    $obCmbApuracaoMetasDespesa->setStyle     ( "width: 250px"       );
    $obCmbApuracaoMetasDespesa->addOption    ( "", "Selecione"      );
    $obCmbApuracaoMetasDespesa->addOption    ( "1", "Mensal"        );
    $obCmbApuracaoMetasDespesa->addOption    ( "2", "Bimestral"     );
    $obCmbApuracaoMetasDespesa->addOption    ( "3", "Trimestral"    );
    $obCmbApuracaoMetasDespesa->addOption    ( "4", "Quadrimestral" );
    $obCmbApuracaoMetasDespesa->addOption    ( "6", "Semestral"     );
    $obCmbApuracaoMetasDespesa->setNull      ( false );
}
/*  */

$obTxtMascaraReceita = new TextBox;
$obTxtMascaraReceita->setName       ( "stMascReceita" );
$obTxtMascaraReceita->setValue      ( $stMascReceita );
$obTxtMascaraReceita->setRotulo     ( "Máscara de Classificação da Receita" );
$obTxtMascaraReceita->setTitle      ( "Informe a máscara de classificação da receita." );
$obTxtMascaraReceita->setSize       ( 35 );
$obTxtMascaraReceita->setMaxLength  ( "" );
$obTxtMascaraReceita->setNull       ( false );
$obTxtMascaraReceita->setDecimais   ( 0 );
//$obTxtMascaraReceita->setFloat      ( 2 );
$obTxtMascaraReceita->obEvento->setOnChange("validaCampos('validaMascaraReceita');");
$obTxtMascaraReceita->obEvento->setOnKeyPress("return validaExpressao( this, event, '[9.]');");

$obTxtMascaraReceitaDedutora = new TextBox;
$obTxtMascaraReceitaDedutora->setName       ( "stMascReceitaDedutora" );
$obTxtMascaraReceitaDedutora->setValue      ( $stMascReceitaDedutora );
$obTxtMascaraReceitaDedutora->setRotulo     ( "Máscara de Classificação da Receita Dedutora" );
$obTxtMascaraReceitaDedutora->setTitle      ( "Informe a máscara de classificação da receita dedutora." );
$obTxtMascaraReceitaDedutora->setSize       ( 35 );
$obTxtMascaraReceitaDedutora->setMaxLength  ( "" );
$obTxtMascaraReceitaDedutora->setNull       ( false );
$obTxtMascaraReceitaDedutora->setDecimais   ( 0 );
$obTxtMascaraReceitaDedutora->obEvento->setOnChange("validaCampos('validaMascaraReceitaDedutora');");
$obTxtMascaraReceitaDedutora->obEvento->setOnKeyPress("return validaExpressao( this, event, '[9.]');");

$obTxtMascaraPosicaoDespesa = new TextBox;
$obTxtMascaraPosicaoDespesa->setName      ( "stMascPosicaoDespesa" );
$obTxtMascaraPosicaoDespesa->setValue     ( $stMascPosicaoDespesa );
$obTxtMascaraPosicaoDespesa->setRotulo    ( "Máscara da Despesa" );
$obTxtMascaraPosicaoDespesa->setTitle     ( "Informe a máscara da despesa." );
$obTxtMascaraPosicaoDespesa->setSize      ( 35 );
$obTxtMascaraPosicaoDespesa->setMaxLength ( "" );
$obTxtMascaraPosicaoDespesa->setNull      ( false );
$obTxtMascaraPosicaoDespesa->setDecimais  ( 0 );
//$obTxtMascaraPosicaoDespesa->setFloat     ( 2 );
$obTxtMascaraPosicaoDespesa->obEvento->setOnChange("validaCampos('validaMascaraDespesa');");
$obTxtMascaraPosicaoDespesa->obEvento->setOnKeyPress("return validaExpressao( this, event, '[9.]');");

$obTxtMascaraDespesa = new TextBox;
$obTxtMascaraDespesa->setName      ( "stMascDespesa" );
$obTxtMascaraDespesa->setValue     ( $stMascDespesa );
$obTxtMascaraDespesa->setRotulo    ( "Máscara da Despesa" );
$obTxtMascaraDespesa->setTitle     ( "Informe a máscara da despesa." );
$obTxtMascaraDespesa->setSize      ( 35 );
$obTxtMascaraDespesa->setMaxLength ( "" );
$obTxtMascaraDespesa->setNull      ( false );
$obTxtMascaraDespesa->setDecimais  ( 0 );
//$obTxtMascaraDespesa->setFloat     ( 2 );
$obTxtMascaraDespesa->obEvento->setOnChange("validaCampos('validaMascDespesa');");
$obTxtMascaraDespesa->obEvento->setOnKeyPress("return validaExpressao( this, event, '[9.]');");
//$obTxtMascaraDespesa->obEvento->setOnKeyPress  ("mascaraDinamico('$stMascDespesa', this, event);");

$obRdDestinacao = new SimNao;
$obRdDestinacao->setRotulo ( "Utilizar Destinação de Recursos" );
$obRdDestinacao->setChecked( ( $obRConfiguracaoOrcamento->getDestinacaoRecurso() == 'true' ? 'SIM' : 'NAO') );
$obRdDestinacao->obRadioSim->obEvento->setOnClick ( "executaFuncaoAjax('montaDestinacaoRecurso'); ");
$obRdDestinacao->obRadioNao->obEvento->setOnClick ( "executaFuncaoAjax('montaRecurso'); ");
$obRdDestinacao->setName   ( "boDestinacao" );

if ($obRConfiguracaoOrcamento->getDestinacaoRecurso() == 'true') {
    $jsOnload = "executaFuncaoAjax('montaDestinacaoRecurso'); ";
} else {
    $jsOnload = "executaFuncaoAjax('montaRecurso'); ";
}

$obSpnRec = new Span;
$obSpnRec->setId ( 'spnRec' );

$obPorcLimiteSuplementacaoDecreto = new Porcentagem();
$obPorcLimiteSuplementacaoDecreto->setTitle('Percentual Autorizado na LOA para Suplementações por Decreto');
$obPorcLimiteSuplementacaoDecreto->setRotulo('Limite Suplementação Decreto');
$obPorcLimiteSuplementacaoDecreto->setName('nuLimiteSuplementacaoDecreto');
$obPorcLimiteSuplementacaoDecreto->setId('nuLimiteSuplementacaoDecreto');
$obPorcLimiteSuplementacaoDecreto->setNull(false);
$obPorcLimiteSuplementacaoDecreto->obEvento->setOnChange(' validaPorcentagem(this.value); ');
$obPorcLimiteSuplementacaoDecreto->setValue($obRConfiguracaoOrcamento->getLimiteSuplementacaoDecreto());

//a partir de 2016 e para MG
$obRdSuplementacaoRigidaRecurso = new SimNao;
$obRdSuplementacaoRigidaRecurso->setRotulo ( 'Utilizar o padrão Suplementação Rigida por Fonte de Recurso' );
$obRdSuplementacaoRigidaRecurso->setName   ( 'stSuplementacaoRegidaRecurso' );
$obRdSuplementacaoRigidaRecurso->setId     ( 'stSuplementacaoRegidaRecurso' );
$obRdSuplementacaoRigidaRecurso->setChecked( ($obRConfiguracaoOrcamento->getSuplementacaoRigidaRecurso() == 'sim' ? 'S' : 'N') );
$obRdSuplementacaoRigidaRecurso->setNull   (false);

// Define Objeto Hidden para Posição do Digito de Identificação
$obHdnPosicaoDigitoID = new Hidden();
$obHdnPosicaoDigitoID->setName  ( "inPosicaoDigitoID"                                   );
$obHdnPosicaoDigitoID->setValue ( $obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID() );

$obLblPosicaoDigitoID = new Label;
$obLblPosicaoDigitoID->setValue     ( $obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID() );
$obLblPosicaoDigitoID->setRotulo    ( "Posição do Dígito de Identificação"  );

$obTxtDigitoIDProjeto = new TextBox;
$obTxtDigitoIDProjeto->setName      ( "inDigitoIDProjeto" );
$obTxtDigitoIDProjeto->setValue     ( $obRConfiguracaoOrcamento->getNumPAODigitosIDProjeto() );
$obTxtDigitoIDProjeto->setRotulo    ( "Dígitos de Identificação do Projeto" );
$obTxtDigitoIDProjeto->setTitle     ( "Informe os dígitos de identificação do projeto." );
$obTxtDigitoIDProjeto->setSize      ( "21"  );
$obTxtDigitoIDProjeto->setNull      ( false );
$obTxtDigitoIDProjeto->obEvento->setOnChange("validaCampos('IDProjeto');");
$obTxtDigitoIDProjeto->obEvento->setOnKeyPress("return validaExpressao( this, event, '[0-9,]');");

$obTxtDigitoIDAtividade = new TextBox;
$obTxtDigitoIDAtividade->setName      ( "inDigitoIDAtividade" );
$obTxtDigitoIDAtividade->setValue     ( $obRConfiguracaoOrcamento->getNumPAODigitosIDAtividade() );
$obTxtDigitoIDAtividade->setRotulo    ( "Dígitos de Identificação da Atividade" );
$obTxtDigitoIDAtividade->setTitle     ( "Informe os dígitos de identificação da atividade." );
$obTxtDigitoIDAtividade->setSize      ( "21"  );
$obTxtDigitoIDAtividade->setNull      ( false );
$obTxtDigitoIDAtividade->obEvento->setOnChange("validaCampos('IDAtividade');");
$obTxtDigitoIDAtividade->obEvento->setOnKeyPress("return validaExpressao( this, event, '[0-9,]');");

$obTxtDigitoIDEspecial = new TextBox;
$obTxtDigitoIDEspecial->setName      ( "inDigitoIDEspecial" );
$obTxtDigitoIDEspecial->setValue     ( $obRConfiguracaoOrcamento->getNumPAODigitosIDOperEspeciais() );
$obTxtDigitoIDEspecial->setRotulo    ( "Dígitos de Identificação da Operação Especial" );
$obTxtDigitoIDEspecial->setTitle     ( "Informe os dígitos de identificação da operação especial." );
$obTxtDigitoIDEspecial->setSize      ( "21"  );
$obTxtDigitoIDEspecial->setNull      ( false );
$obTxtDigitoIDEspecial->obEvento->setOnChange("validaCampos('IDEspecial');");
$obTxtDigitoIDEspecial->obEvento->setOnKeyPress("return validaExpressao( this, event, '[0-9,]');");

$obTxtDigitoIDNaoOrcamentarios = new TextBox;
$obTxtDigitoIDNaoOrcamentarios->setName      ( "inDigitoIDNaoOrcamentarios" );
$obTxtDigitoIDNaoOrcamentarios->setValue     ( $obRConfiguracaoOrcamento->getNumPAODigitosIDNaoOrcamentarios() );
$obTxtDigitoIDNaoOrcamentarios->setRotulo    ( "Dígitos de Identificação de Não Orçamentários" );
$obTxtDigitoIDNaoOrcamentarios->setTitle     ( "Informe os dígitos de identificação de não orçamentários." );
$obTxtDigitoIDNaoOrcamentarios->setSize      ( "21"  );
$obTxtDigitoIDNaoOrcamentarios->setNull      ( false );
$obTxtDigitoIDNaoOrcamentarios->obEvento->setOnChange("validaCampos('IDNaoOrcamentarios');");
$obTxtDigitoIDNaoOrcamentarios->obEvento->setOnKeyPress("return validaExpressao( this, event, '[0-9,]');");

$obTxtContador = new TextBox;
$obTxtContador->setName      ( "inCodContador" );
$obTxtContador->setValue     ( $obRConfiguracaoOrcamento->getCodContador() );
$obTxtContador->setRotulo    ( "Profissão Contador" );
$obTxtContador->setTitle     ( "Selecione a profissão contador." );
$obTxtContador->setSize      ( "5" );
$obTxtContador->setMaxLength ( "5" );
$obTxtContador->setNull      ( false );

$obErro = $obRProfissao->listarProfissao( $rsResponsavel );
$obCmbContador = new Select;
$obCmbContador->setName       ( "stCodContador" );
$obCmbContador->setValue      ( $obRConfiguracaoOrcamento->getCodContador() );
$obCmbContador->addOption     ( "", "Selecione" );
$obCmbContador->setCampoId    ( "cod_profissao" );
$obCmbContador->setCampoDesc  ( "nom_profissao" );
$obCmbContador->preencheCombo ( $rsResponsavel );
$obCmbContador->setNull       ( false );
$rsResponsavel->setPrimeiroElemento();

$obTxtTecContabil = new TextBox;
$obTxtTecContabil->setName      ( "inCodTecContabil" );
$obTxtTecContabil->setValue     ( $obRConfiguracaoOrcamento->getCodTecContabil() );
$obTxtTecContabil->setRotulo    ( "Profissão Técnico Contábil" );
$obTxtTecContabil->setTitle     ( "Selecione a profissão técnico contábil." );
$obTxtTecContabil->setSize      ( "5" );
$obTxtTecContabil->setMaxLength ( "5" );
$obTxtTecContabil->setNull      ( false );

$obCmbTecContabil = new Select;
$obCmbTecContabil->setName       ( "stCodTecContabil" );
$obCmbTecContabil->setValue      ( $obRConfiguracaoOrcamento->getCodTecContabil() );
$obCmbTecContabil->addOption     ( "", "Selecione" );
$obCmbTecContabil->setCampoId    ( "cod_profissao" );
$obCmbTecContabil->setCampoDesc  ( "nom_profissao" );
$obCmbTecContabil->preencheCombo ( $rsResponsavel );
$obCmbTecContabil->setNull       ( false );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setLarguraRotulo         ( 22 );
$obFormulario->setAjuda                 ("UC-02.01.01"          );
$obFormulario->addForm                  ( $obForm               );
$obFormulario->addHidden                ( $obHdnAcao            );
$obFormulario->addHidden                ( $obHdnCtrl            );
$obFormulario->addHidden                ( $obHdnModulo          );
$obFormulario->addHidden                ( $obHdnAcaoModulo      );
$obFormulario->addHidden                ( $obHdnPosicaoDigitoID );

$obFormulario->addTitulo                ( "Dados para Configuração"     );
$obFormulario->addComponenteComposto    ( $obTxtCodigoEntidadePrefeitura, $obCmbNomeEntidadePrefeitura );
$obFormulario->addComponenteComposto    ( $obTxtCodigoEntidadeCamara, $obCmbNomeEntidadeCamara );
$obFormulario->addComponenteComposto    ( $obTxtCodigoEntidadeRPPS, $obCmbNomeEntidadeRPPS );
$obFormulario->addComponenteComposto    ( $obTxtCodigoEntidadeConsorcio, $obCmbNomeEntidadeConsorcio );

if (!$obErro->ocorreu() ) {
    if ($rsEmpenhos->getCampo("empenhos") >= 1) {
        $obFormulario->addComponente        ( $obLblFormaExecucao );
        $obFormulario->addHidden            ( $obHdnForma         );
    } else {
        $obFormulario->addComponenteComposto    ( $obCmbCodFormaExecucao, $obCmbFormaExecucao );
    }
} else {
    exit($obErro->getDescricao());
}

if (Sessao::getExercicio() < '2014') {
    $obFormulario->addComponenteComposto( $obCmbCodApuracaoMetas       , $obCmbApuracaoMetas        );
} else {
    $obFormulario->addComponenteComposto( $obCmbCodApuracaoMetasReceita, $obCmbApuracaoMetasReceita );
    $obFormulario->addComponenteComposto( $obCmbCodApuracaoMetasDespesa, $obCmbApuracaoMetasDespesa );
}

$obFormulario->addComponente            ( $obTxtMascaraReceita          );
$obFormulario->addComponente            ( $obTxtMascaraReceitaDedutora  );
$obFormulario->addComponente            ( $obTxtMascaraPosicaoDespesa   );
$obFormulario->addComponente            ( $obTxtMascaraDespesa          );
$obFormulario->addComponente            ( $obRdDestinacao               );
$obFormulario->addSpan                  ( $obSpnRec                     );
$obFormulario->addComponente            ( $obPorcLimiteSuplementacaoDecreto );

if ( Sessao::getExercicio() > 2015 && SistemaLegado::isTCEMG($boTransacao) )
    $obFormulario->addComponente            ( $obRdSuplementacaoRigidaRecurso );


$obFormulario->addTitulo                ( "Projeto, Atividade, Operação Especial" );
$obFormulario->addComponente            ( $obLblPosicaoDigitoID         );
$obFormulario->addComponente            ( $obTxtDigitoIDProjeto         );
$obFormulario->addComponente            ( $obTxtDigitoIDAtividade       );
$obFormulario->addComponente            ( $obTxtDigitoIDEspecial        );
$obFormulario->addComponente            ( $obTxtDigitoIDNaoOrcamentarios);

$obFormulario->addTitulo                ( "Responsável Contábil" );
$obFormulario->addComponenteComposto    ( $obTxtContador, $obCmbContador       );
$obFormulario->addComponenteComposto    ( $obTxtTecContabil, $obCmbTecContabil );

$obFormulario->OK();
$obFormulario->show();
include_once( $pgJS );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
