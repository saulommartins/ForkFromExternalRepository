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
* Página de Formulario de Filtro da Configuração de Salário Família

* Data de Criação: 28/04/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 31475 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"                              );

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $request->get('stAcao');

Sessao::write("filtroRelatorio",array());

$obRFolhaPagamentoPrevidencia = new RFolhaPagamentoPrevidencia;
$obRFolhaPagamentoPrevidencia->listarTodosRegimePrevidencia( $rsRegimePrevidencia );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSalarioFamilia";
$pgList     = "LS".$stPrograma.".php";
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obFiltroDtVigencia = new Data;
$obFiltroDtVigencia->setName    ( "dtFiltroVigencia"                  );
$obFiltroDtVigencia->setRotulo  ( "Vigência"                    );
$obFiltroDtVigencia->setNull    ( true                          );
$obFiltroDtVigencia->setTitle   ( 'Informe a vigência para o filtro de busca.' );

$rsRegimePrevidencia->setPrimeiroElemento();
//Define objetos RADIO para armazenar o TIPO dos Itens
for ( $i=0 ; $i<$rsRegimePrevidencia->getNumLinhas() ; $i++ ) {
    $obRdbRegimeRGPS[$i] = new Radio;
    $obRdbRegimeRGPS[$i]->setRotulo  ( "Regime Previdenciário"                                   );
    $obRdbRegimeRGPS[$i]->setTitle   ( "Informe o regime previdenciário para o filtro de busca." );
    $obRdbRegimeRGPS[$i]->setName    ( "stFiltroCodRegime"                                             );
    $obRdbRegimeRGPS[$i]->setid      ( "stFiltroCodRegime".$rsRegimePrevidencia->getCampo("descricao") );
    $obRdbRegimeRGPS[$i]->setLabel   ( $rsRegimePrevidencia->getCampo("descricao")               );
    $obRdbRegimeRGPS[$i]->setValue   ( $rsRegimePrevidencia->getCampo("cod_regime_previdencia")  );
    if ( $i == 0 )
        $obRdbRegimeRGPS[$i]->setChecked ( true                                                  );
    else
        $obRdbRegimeRGPS[$i]->setChecked ( false                                                 );
    $obRdbRegimeRGPS[$i]->setNull    ( false                                                     );
    $rsRegimePrevidencia->proximo();
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo         ( "Dados para o filtro" );
$obFormulario->addHidden         ( $obHdnAcao            );
if ($stAcao != "excluir") {
    $obFormulario->addComponente     ( $obFiltroDtVigencia   );
}
$obFormulario->agrupaComponentes ( $obRdbRegimeRGPS      );

$obFormulario->ok();

$obFormulario->setFormFocus( $obFiltroDtVigencia->getId() );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
