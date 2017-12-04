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
  * Página que gera o Relatório Anexo III - TCEMG
  * Data de Criação: 15/07/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: OCGeraRelatorioAnexoIII.php 66368 2016-08-18 19:28:46Z michel $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

#TCEMGRelatorioAnexoIII.rptdesign
$preview = new PreviewBirt(6,55,3);
$preview->setTitulo('Relatório de Fundo de Manutenção e Desenvolvimento da Educação Básica e de Valorização dos Profissionais da Educação');
$preview->setVersaoBirt( '2.5.0' );

$stMes = 1;
if ($request->get('stMes')){
    $stMes = $request->get('stMes');
}else{    
    if ( $request->get('stPeriodoFinal') ) {
        list($dia,$mes,$ano) = explode('/', $request->get('stPeriodoFinal'));
        $stMes = $mes;
    }    
}

switch ($stMes) {
    case 01:
    case 02:
        $inBimestre = 1;
        break;
    case 03:
    case 04:
        $inBimestre = 2;
        break;
    case 05:
    case 06:
        $inBimestre = 3;
        break;
    case 07:
    case 08:
        $inBimestre = 4;
        break;        
    case 09:
    case 10:
        $inBimestre = 5;
        break;        
    case 11:
    case 12:
        $inBimestre = 6;
        break;        
}

list($dia,$mes,$ano) = explode('/', $request->get('stDataInicial'));
$stDataInicialAnterior = $dia.'/'.$mes.'/'.($ano-1);

list($dia,$mes,$ano) = explode('/', $request->get('stDataFinal'));
$stDataFinalAnterior = $dia.'/'.$mes.'/'.($ano-1);

$inCodEntidade = implode($request->get('inCodEntidade'), ',');
$boRestos =  "true";

$preview->addParametro("exercicio_anterior" , (Sessao::getExercicio()-1)        );
$preview->addParametro("bimestre"           , $inBimestre                       );
$preview->addParametro("entidade"           , $inCodEntidade                    );
$preview->addParametro("tipo_relatorio"     , $request->get("stTipoRelatorio")  );
$preview->addParametro("dtInicialAnterior"  , $stDataInicialAnterior            );
$preview->addParametro("dtFinalAnterior"    , $stDataFinalAnterior              );
$preview->addParametro("dtInicial"          , $request->get("stDataInicial")    );
$preview->addParametro("dtFinal"            , $request->get("stDataFinal")      );
$preview->addParametro("boRestos"           , $boRestos);

$preview->preview();
?>