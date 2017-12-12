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
     * Classe de mapeamento para a tabela IMOBILIARIO.VIGENCIA
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMVigencia.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.02
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.VIGENCIA
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMVigencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMVigencia()
{
    parent::Persistente();
    $this->setTabela('imobiliario.vigencia');

    $this->setCampoCod('cod_vigencia');
    $this->setComplementoChave('');

    $this->AddCampo('cod_vigencia','integer',true,'',true,false);
    $this->AddCampo('dt_inicio','date',true,'',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

/**
    * Seleciona o campos referentes a data atual
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaVigenciaAtual(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSQL = $this->montaRecuperaVigenciaAtual();
    $this->setDebug( $stSQL );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );

    return $obErro;
}
/**
    * Monta consulta para recuperar a data atual
    * @access Private
    * @return String $stSQL
*/
function montaRecuperaVigenciaAtual()
{
    $stQuebra = "\n";
    $stSQL  = " SELECT                                              ".$stQuebra;
    $stSQL .= "     cod_vigencia,                                   ".$stQuebra;
    $stSQL .= "     to_char(dt_inicio,'dd/mm/yyyy') as dtinicio,    ".$stQuebra;
    $stSQL .= "     timestamp                                       ".$stQuebra;
    $stSQL .= " FROM                                                ".$stQuebra;
    $stSQL .= "     ".$this->getTabela()."                          ".$stQuebra;
    $stSQL .= " WHERE                                               ".$stQuebra;
    $stSQL .= "     TO_CHAR(dt_inicio,'dd/mm/yyyy') <= TO_CHAR( NOW(), 'yyyy-mm-dd' ) AND ".$stQuebra;
    $stSQL .= "     cod_vigencia > 0                                ".$stQuebra;
    $stSQL .= " ORDER BY dt_inicio DESC                             ".$stQuebra;
    $stSQL .= "     LIMIT 1                                         ".$stQuebra;

    return $stSQL;
}

/**
    * Seleciona todos
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaTodos(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSQL = $this->montaRecuperaTodos($stFiltro);
    $this->setDebug( $stSQL );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );

    return $obErro;
}
/**
    * Monta consulta para recuperar todas as vigencias
    * @access Private
    * return String $stSql
*/
function montaRecuperaTodos($stFiltro="")
{
    $stSql  = " SELECT                                              ";
    $stSql .= "     cod_vigencia,                                   ";
    $stSql .= "     to_char(dt_inicio,'dd/mm/yyyy') as dtinicio,    ";
    $stSql .= "     timestamp                                       ";
    $stSql .= " FROM                                                ";
    $stSql .= "     ".$this->getTabela()."                          ";
    if ($stFiltro) {
        $stSql .= $stFiltro;
    }
    $stSql .= " ORDER BY                                            ";
    $stSql .= "     dtinicio    DESC                                ";

    return $stSql;

}

/**
    * Seleciona todos
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaDataUltimaVigencia(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSQL = $this->montaRecuperaDataUltimaVigencia($stFiltro);
    $this->setDebug( $stSQL );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );

    return $obErro;
}
/**
    * Monta consulta para recuperar todas as vigencias
    * @access Private
    * return String $stSql
*/
function montaRecuperaDataUltimaVigencia()
{
    $stSql  = " SELECT                                              ";
    $stSql .= "     to_char(dt_inicio,'dd/mm/yyyy') as dtinicio     ";
    $stSql .= " FROM                                                ";
    $stSql .= "     ".$this->getTabela()."                          ";
    $stSql .= " ORDER BY                                            ";
    $stSql .= "     cod_vigencia DESC                               ";
    $stSql .= " LIMIT 1                                             ";

    return $stSql;

}

}
