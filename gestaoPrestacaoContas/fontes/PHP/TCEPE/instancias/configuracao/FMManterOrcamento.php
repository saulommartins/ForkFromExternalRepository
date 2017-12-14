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
     * 
    * Data de Criação   : 26/09/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: FMManterOrcamento.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once(CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");

$stPrograma = "ManterOrcamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Cria um TextBox para o ano de vigencia da lei orcamentaria
$obTextAnoVigencia = new TextBox;
$obTextAnoVigencia->setRotulo('Ano Vigência');
$obTextAnoVigencia->setName  ('ano_vigencia');
$obTextAnoVigencia->setId    ('ano_vigencia');
$obTextAnoVigencia->setNull  (false);
$obTextAnoVigencia->setSize  (4);
$obTextAnoVigencia->setValue ($ano_vigencia);

//Cria um campo data para a data de aprovacao da LOA
$obDtAprovacaoLOA = new Data();
$obDtAprovacaoLOA->setRotulo( 'Data de Aprovação da LOA');
$obDtAprovacaoLOA->setName('dtAprovacaoLOA');
$obDtAprovacaoLOA->setId('dtAprovacaoLOA');
$obDtAprovacaoLOA->setNull( false );

### Cria um campo para o numero da lei orcamentaria do LOA ###
$obIPopUpLeiLOA = new IPopUpNorma();
$obIPopUpLeiLOA->obInnerNorma->setId('stNomeLeiLOA');
$obIPopUpLeiLOA->obInnerNorma->obCampoCod->stId = 'inCodLeiLOA';
$obIPopUpLeiLOA->obInnerNorma->obCampoCod->setName( "inCodLeiLOA" );
$obIPopUpLeiLOA->obInnerNorma->setRotulo("Número da Lei orçamentária");
$obIPopUpLeiLOA->obInnerNorma->obCampoCod->obEvento->setOnChange(" montaParametrosGET('buscaLeiLOA', 'inCodLeiLOA'); ");

//Cria um campo data para a data de aprovacao da LDO
$obDtAprovacaoLDO = new Data();
$obDtAprovacaoLDO->setRotulo('Data de Aprovação da LDO');
$obDtAprovacaoLDO->setName('dtAprovacaoLDO');
$obDtAprovacaoLDO->setId('dtAprovacaoLDO');
$obDtAprovacaoLDO->setNull( false );

### Cria um campo para o numero da lei orcamentaria do LDO ###
$obIPopUpLeiLDO = new IPopUpNorma();
$obIPopUpLeiLDO->obInnerNorma->setId('stNomeLeiLDO');
$obIPopUpLeiLDO->obInnerNorma->obCampoCod->stId = 'inCodLeiLDO';
$obIPopUpLeiLDO->obInnerNorma->obCampoCod->setName( "inCodLeiLDO" );
$obIPopUpLeiLDO->obInnerNorma->setRotulo("Número da LDO");
$obIPopUpLeiLDO->obInnerNorma->obCampoCod->obEvento->setOnChange(" montaParametrosGET('buscaLeiLDO', 'inCodLeiLDO'); ");

//Cria um campo data para a data de aprovacao do PPA
$obDtAprovacaoPPA = new Data();
$obDtAprovacaoPPA->setRotulo('Data de Aprovação do PPA');
$obDtAprovacaoPPA->setName('dtAprovacaoPPA');
$obDtAprovacaoPPA->setId('dtAprovacaoPPA');
$obDtAprovacaoPPA->setNull( false );

### Lei do PPA ###
$obIPopUpLeiPPA = new IPopUpNorma();
$obIPopUpLeiPPA->obInnerNorma->setId('stNomeLeiPPA');
$obIPopUpLeiPPA->obInnerNorma->obCampoCod->stId = 'inCodLeiPPA';
$obIPopUpLeiPPA->obInnerNorma->obCampoCod->setName( "inCodLeiPPA" );
$obIPopUpLeiPPA->obInnerNorma->setRotulo("Número do PPA");
$obIPopUpLeiPPA->obInnerNorma->obCampoCod->obEvento->setOnChange(" montaParametrosGET('buscaLeiPPA', 'inCodLeiPPA'); ");

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo('Configurações do Orçamento');
$obFormulario->addComponente( $obTextAnoVigencia );
$obFormulario->addComponente( $obDtAprovacaoLOA );
$obIPopUpLeiLOA->geraFormulario($obFormulario);
$obFormulario->addComponente( $obDtAprovacaoLDO );
$obIPopUpLeiLDO->geraFormulario($obFormulario);
$obFormulario->addComponente( $obDtAprovacaoPPA );
$obIPopUpLeiPPA->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

echo "<script type=\"text/javascript\">             \r\n";
echo "    ajaxJavaScript('".$pgOcul."?".Sessao::getId()."', 'buscaDados');     \r\n";
echo "</script>   \r\n";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
