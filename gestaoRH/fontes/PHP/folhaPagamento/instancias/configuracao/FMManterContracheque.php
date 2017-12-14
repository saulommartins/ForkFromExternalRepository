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
    * Formulário
    * Data de Criação: 01/08/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: alex $
    $Date: 2008-03-12 16:28:01 -0300 (Qua, 12 Mar 2008) $

    * Casos de uso: uc-04.05.63
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$stPrograma = 'ManterContracheque';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoContracheque.class.php");
$obTFolhaPagamentoConfiguracaoContracheque            = new TFolhaPagamentoConfiguracaoContracheque();

$jsOnload = "executaFuncaoAjax('montaConfiguracao');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$arCampos = array(
array("cod"=>"nome_entidade"                ,"desc"=>"Nome Entidade"),
array("cod"=>"estado_entidade"              ,"desc"=>"Estado da Entidade"),
array("cod"=>"registro"                     ,"desc"=>"Matrícula"),
array("cod"=>"nom_cgm"                      ,"desc"=>"Nome do Servidor"),
array("cod"=>"cbo"                          ,"desc"=>"CBO"),
array("cod"=>"competencia"                  ,"desc"=>"Competência"),
array("cod"=>"tipo_calculo"                 ,"desc"=>"Tipo de Cálculo"),
array("cod"=>"funcao_especialidade"         ,"desc"=>"Função/Especialidade"),
array("cod"=>"orgao"                        ,"desc"=>"Lotação"),
array("cod"=>"local"                        ,"desc"=>"Local"),
array("cod"=>"pispasep"                     ,"desc"=>"PisPasep"),
array("cod"=>"cpf"                          ,"desc"=>"CPF"),
array("cod"=>"cnpj"                         ,"desc"=>"CNPJ"),
array("cod"=>"rg"                           ,"desc"=>"RG"),
array("cod"=>"num_banco"                    ,"desc"=>"Código Banco"),
array("cod"=>"nom_banco"                    ,"desc"=>"Descrição Banco"),
array("cod"=>"num_agencia"                  ,"desc"=>"Código da Agência"),
array("cod"=>"nom_agencia"                  ,"desc"=>"Descrição da Agência"),
array("cod"=>"nr_conta"                     ,"desc"=>"Conta Corrente"),
array("cod"=>"eventos"                      ,"desc"=>"Eventos"),
array("cod"=>"desc_eventos"                 ,"desc"=>"Descrição Eventos"),
array("cod"=>"quantidades"                  ,"desc"=>"Quantidade"),
array("cod"=>"proventos"                    ,"desc"=>"Proventos"),
array("cod"=>"descontos"                    ,"desc"=>"Descontos"),
array("cod"=>"mensagem"                     ,"desc"=>"Mensagem"),
array("cod"=>"total_vencimentos"            ,"desc"=>"Total de Vencimentos"),
array("cod"=>"total_descontos"              ,"desc"=>"Total de Descontos"),
array("cod"=>"liquido"                      ,"desc"=>"Líquido"),
array("cod"=>"salario_base"                 ,"desc"=>"Salário Base"),
array("cod"=>"base_inss"                    ,"desc"=>"Base INSS"),
array("cod"=>"base_fgts"                    ,"desc"=>"Base FGTS"),
array("cod"=>"recolhido_fgts"               ,"desc"=>"Recolhido FGTS"),
array("cod"=>"base_irrf"                    ,"desc"=>"Base IRRF"),
array("cod"=>"faixa_irrf"                   ,"desc"=>"Faixa IRRF"),
array("cod"=>"dependentes"                  ,"desc"=>"Dependentes"),
array("cod"=>"desdobramento"                ,"desc"=>"Desdobramento"),
array("cod"=>"dt_posse"                     ,"desc"=>"Data da Posse"),
array("cod"=>"dt_admissao"                  ,"desc"=>"Data de Admissão")
);
$rsCampos = new RecordSet();
$rsCampos->preenche($arCampos);
$rsCampos->ordena("desc","ASC",SORT_STRING);
Sessao::write("arCampos",$arCampos);
Sessao::write("arConfiguracoes",array());

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoContracheque.class.php");
$obTFolhaPagamentoConfiguracaoContracheque = new TFolhaPagamentoConfiguracaoContracheque();
$obTFolhaPagamentoConfiguracaoContracheque->recuperaTodos($rsConfiguracao,"","linha,coluna");
$arConfiguracoes = Sessao::read("arConfiguracoes");
while (!$rsConfiguracao->eof()) {
    $inId = count($arConfiguracoes)+1;
    $arCampos = Sessao::read("arCampos");
    foreach ($arCampos as $arCampo) {
        if ($arCampo["cod"] == $rsConfiguracao->getCampo("nom_campo")) {
            $stCampoDesc = $arCampo["desc"];
        }
    }
    $arConfiguracao["inId"]         = $inId;
    $arConfiguracao["stCampoId"]    = $rsConfiguracao->getCampo("nom_campo");
    $arConfiguracao["stCampoDesc"]  = $stCampoDesc;
    $arConfiguracao["inColuna"]     = $rsConfiguracao->getCampo("coluna");
    $arConfiguracao["inLinha"]      = $rsConfiguracao->getCampo("linha");
    $arConfiguracoes[]              = $arConfiguracao;
    $rsConfiguracao->proximo();
}
Sessao::write("arConfiguracoes",$arConfiguracoes);

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

$obCmbCampo = new Select();
$obCmbCampo->setRotulo("Campo");
$obCmbCampo->setTitle("Selecione o campo para apresentação no contracheque.");
$obCmbCampo->setName("stCampo");
$obCmbCampo->setId("stCampo");
$obCmbCampo->setNullBarra(false);
$obCmbCampo->addOption("","Selecione");
$obCmbCampo->setCampoId("cod");
$obCmbCampo->setCampoDesc("desc");
$obCmbCampo->preencheCombo($rsCampos);

$obTxtColuna = new TextBox();
$obTxtColuna->setRotulo("Coluna");
$obTxtColuna->setTitle("Informe o número da coluna que será posicionado o campo.");
$obTxtColuna->setName("inColuna");
$obTxtColuna->setInteiro(true);
$obTxtColuna->setNullBarra(false);

$obTxtLinha = new TextBox();
$obTxtLinha->setRotulo("Linha");
$obTxtLinha->setTitle("Informe o número da coluna que será posicionado o campo.");
$obTxtLinha->setName("inLinha");
$obTxtLinha->setInteiro(true);
$obTxtLinha->setNullBarra(false);

$obSpnConfiguracoes = new Span();
$obSpnConfiguracoes->setId("spnConfiguracoes");

$arComponentesConfiguracao = array($obCmbCampo,
                                    $obTxtColuna,
                                    $obTxtLinha);

$obFormulario = new Formulario();
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo("Configuração Contracheque");
$obFormulario->addComponente($obCmbCampo);
$obFormulario->addComponente($obTxtColuna);
$obFormulario->addComponente($obTxtLinha);
$obFormulario->IncluirAlterar("Configuracao",$arComponentesConfiguracao,true);
$obFormulario->addSpan($obSpnConfiguracoes);
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
