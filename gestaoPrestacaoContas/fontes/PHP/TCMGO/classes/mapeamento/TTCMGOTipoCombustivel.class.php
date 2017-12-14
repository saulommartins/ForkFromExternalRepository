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
    * Classe de mapeamento da tabela tcmgo.combustivel
    * Data de Criação   : 23/12/2008

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOTipoCombustivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGOTipoCombustivel()
{
    parent::Persistente();
    $this->setTabela("tcmgo.tipo_combustivel");

    $this->setCampoCod('cod_tipo');

    $this->AddCampo( 'cod_combustivel' , 'integer'    , true  , ''   , true  , true  );
    $this->AddCampo( 'descricao'       , 'varchar'    , true  , ''   , true  , true  );
}
/*
function recuperaDetalhamentoContrato(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDetalhamentoContrato().$stFiltro;
    $stSql.= " GROUP BY ce.cod_contrato           \n";

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaDetalhamentoContrato()
{
    $stSql  = " SELECT ce.cod_contrato                                  \n";
    $stSql .= "     , ce.exercicio                                      \n";
}
*/
}
?>
