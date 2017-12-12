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
   /*
    * Formulario de Filtro para a geracao dos arquivos do TCM/MG
    * Data de Criação   : 15/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    * @ignore
    * $Id: FLExportarArquivos.php 63325 2015-08-18 17:13:32Z franver $
    * $Rev: 63325 $
    * $Author: franver $
    * $Date: 2015-08-18 14:13:32 -0300 (Tue, 18 Aug 2015) $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$pgOcul 	= "OCExportarArquivos.php"	;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('link');

//$rsArqExport = $rsAtributos = new RecordSet;

$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnPaginaExportacao = new Hidden;
$obHdnPaginaExportacao->setName ('hdnPaginaExportacao');
$obHdnPaginaExportacao->setValue('../../../TCEMG/instancias/exportacao/' . $pgOcul);

//Instancia um hidden para o stCtrl
$obHdnCtrl = new hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue('montaArquivos');

//Instancia um componente select para o tipo de poder
$obSlTipoPoder = new Select();
$obSlTipoPoder->setName    ('stTipoPoder');
$obSlTipoPoder->setId      ('stTipoPoder');
$obSlTipoPoder->setRotulo  ('Tipo de Poder');
$obSlTipoPoder->setTitle   ('Selecione o tipo de poder');
$obSlTipoPoder->setNull    (false);
$obSlTipoPoder->addOption  ('','Selecione');
$obSlTipoPoder->addOption  ('executivo','Executivo');
$obSlTipoPoder->addOption  ('legislativo','Legislativo');
$obSlTipoPoder->obEvento->setOnChange("montaParametrosGET('preencheEntidade','stTipoPoder');");

//Instancia um select multiplo para as entidades
$obCmbEntidade = new SelectMultiplo();
$obCmbEntidade->setName            ('inCodEntidade');
$obCmbEntidade->setRotulo          ('Entidades');
$obCmbEntidade->setNull            (false);
// lista de atributos disponiveis
$obCmbEntidade->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidade->setRecord1    (new RecordSet());
// lista de atributos selecionados
$obCmbEntidade->SetNomeLista2 ('inCodEntidadeSelecionado');
$obCmbEntidade->setRecord2    (new RecordSet());

//Instancia um select para o tipo de periodo
$obSlTipoPeriodo = new Select();
$obSlTipoPeriodo->setName    ('stTipoPeriodo');
$obSlTipoPeriodo->setId      ('stTipoPeriodo');
$obSlTipoPeriodo->setRotulo  ('Tipo de Periodo');
$obSlTipoPeriodo->setTitle   ('Selecione o tipo de periodo');
$obSlTipoPeriodo->addOption  ('','Selecione');
$obSlTipoPeriodo->addOption  ('bimestral','Bimestral');
$obSlTipoPeriodo->setNull    (false);
$obSlTipoPeriodo->obEvento->setOnChange("montaParametrosGET('preenchePeriodo','stTipoPeriodo');
                                         montaParametrosGET('preencheArquivo');");

//Instancia um select para o período
$obSlPeriodo = new Select();
$obSlPeriodo->setName    ('inPeriodo');
$obSlPeriodo->setId      ('inPeriodo');
$obSlPeriodo->setRotulo  ('Período');
$obSlPeriodo->setTitle   ('Selecione um período');
$obSlPeriodo->addOption  ('','Selecione');
$obSlPeriodo->setNull    (false);
$obSlPeriodo->obEvento->setOnChange(" montaParametrosGET('validaArquivoPeriodo'); ");

//Instancia um select multiplo para os arquivos
$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName            ('arArquivos');
$obCmbArquivos->setRotulo          ('Arquivos');
$obCmbArquivos->setNull            (false);
// lista de atributos disponiveis
$obCmbArquivos->SetNomeLista1 ('arArquivosDisponivel');
$obCmbArquivos->setRecord1    (new RecordSet());
// lista de atributos selecionados
$obCmbArquivos->SetNomeLista2 ('arArquivosSelecionado');
$obCmbArquivos->setRecord2    (new RecordSet());

// Tipo Arquivo Individual
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ('stTipoExport');
$obRdbTipoExportArqIndividual->setLabel  ('Arquivos Individuais');
$obRdbTipoExportArqIndividual->setValue  ('individuais');
$obRdbTipoExportArqIndividual->setRotulo ('*Tipo de Exportação');
$obRdbTipoExportArqIndividual->setTitle  ('Tipo de Exportação');
$obRdbTipoExportArqIndividual->setChecked('checked');

// Tipo Arquivo Compactado
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName ('stTipoExport');
$obRdbTipoExportArqCompactado->setLabel('Compactados');
$obRdbTipoExportArqCompactado->setValue('compactados');

//Instancia o formulário
$obForm = new Form();
$obForm->setAction('PRExportador.php');
$obForm->setTarget('telaPrincipal');

//Instancia o formulario
$obFormulario = new Formulario  ();
$obFormulario->addForm          ($obForm);
$obFormulario->addTitulo        ("Dados para geração de arquivos");
$obFormulario->addHidden        ($obHdnPaginaExportacao);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCtrl);

$obFormulario->addComponente    ($obSlTipoPoder);
$obFormulario->addComponente    ($obCmbEntidade);
$obFormulario->addComponente    ($obSlTipoPeriodo);
$obFormulario->addComponente    ($obSlPeriodo);
$obFormulario->addComponente    ($obCmbArquivos);
$obFormulario->agrupaComponentes(array($obRdbTipoExportArqIndividual, $obRdbTipoExportArqCompactado));

$obFormulario->OK  ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
