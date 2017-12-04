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
 * Arquivo que define os filtros do relatório
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

include_once 'JSBalanceteFinanceiro.js';

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obREntidade->listarUsuariosEntidade($rsEntidades, " ORDER BY cod_entidade");
$rsRecordset = new RecordSet;

$obForm = new Form;
$obForm->setAction("OCGeraBalanceteFinanceiro.php" );
$obForm->setTarget("telaPrincipal");

//Combo de Demonstração de Despesa
$obCmbTipoRelatorio= new Select;
$obCmbTipoRelatorio->setRotulo              ( "Demonstrar Despesa" );
$obCmbTipoRelatorio->setName                ( "stDemonstrarDespesa"        );
$obCmbTipoRelatorio->setStyle               ( "width: 200px"              );
$obCmbTipoRelatorio->addOption              ( "", "Selecione"             );
$obCmbTipoRelatorio->addOption              ( "E", "Empenhada"             );
$obCmbTipoRelatorio->addOption              ( "L", "Liquidada"             );
$obCmbTipoRelatorio->addOption              ( "P", "Paga"                  );
$obCmbTipoRelatorio->setNull                ( false );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "" );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

// define objeto Data
$obCmbQuadrimestre = new Select;
$obCmbQuadrimestre->setRotulo        ( "*Periodicidade"           );
$obCmbQuadrimestre->setName          ( "inCodTipoPeriodicidade"         );
$obCmbQuadrimestre->addOption        ( "", "Selecione"           );
$obCmbQuadrimestre->addOption        ( "1", "Mensal"    );
$obCmbQuadrimestre->addOption        ( "2", "Bimestre"    );
$obCmbQuadrimestre->addOption        ( "3", "Quadrimestre"    );
$obCmbQuadrimestre->addOption        ( "4", "Semestre"    );
$obCmbQuadrimestre->setNull          ( true                      );
$obCmbQuadrimestre->setStyle         ( "width: 220px"            );
$obCmbQuadrimestre->obEvento->setOnChange  ( "buscaValor('Periodicidade');");

//Span para  nova combo

$obSpan = new Span();
$obSpan->setId ("spnPeriodo");

//Combo de Demonstração de Despesa
$obCmbTipoEmissao= new Select;
$obCmbTipoEmissao->setRotulo              ( "Tipo de Relatório" );
$obCmbTipoEmissao->setName                ( "inCodTipoRelatorio"        );
$obCmbTipoEmissao->setStyle               ( "width: 200px"              );
$obCmbTipoEmissao->addOption              ( "", "Selecione"             );
$obCmbTipoEmissao->addOption              ( "1", "Por Função"             );
$obCmbTipoEmissao->addOption              ( "2", "Por Categoria Econômica");
$obCmbTipoEmissao->setNull                ( false );

$obHdnStPeriodo = new Hidden;
$obHdnStPeriodo->setId  ('stPeriodo');
$obHdnStPeriodo->setName('stPeriodo');
$obHdnStPeriodo->setValue('');

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ($obHdnStPeriodo);
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obCmbQuadrimestre );
$obFormulario->addSpan      ( $obSpan             );
$obFormulario->addComponente( $obCmbTipoRelatorio    );
$obFormulario->addComponente( $obCmbTipoEmissao    );
$obFormulario->OK();
$obFormulario->show();
?>
