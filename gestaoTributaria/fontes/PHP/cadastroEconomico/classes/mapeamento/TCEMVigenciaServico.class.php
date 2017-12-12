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
  * Classe de mapeamento da tabela ECONOMICO.VIGENCIA_SERVICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMVigenciaServico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.03
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.VIGENCIA_SERVICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMVigenciaServico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMVigenciaServico()
{
    parent::Persistente();
    $this->setTabela('economico.vigencia_servico');

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
    $stSQL  = " SELECT                                              \n";
    $stSQL .= "     cod_vigencia,                                   \n";
    $stSQL .= "     to_char(dt_inicio,'dd/mm/yyyy') as dtinicio,    \n";
    $stSQL .= "     timestamp                                       \n";
    $stSQL .= " FROM                                                \n";
    $stSQL .= "     ".$this->getTabela()."                          \n";
    $stSQL .= " WHERE                                               \n";
    $stSQL .= "     dt_inicio <= NOW() AND \n";
    $stSQL .= "     cod_vigencia > 0                                \n";
    $stSQL .= " ORDER BY dt_inicio DESC                             \n";
    $stSQL .= "     LIMIT 1                                         \n";

    return $stSQL;
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
    $stSql  = " SELECT                                                  ";
    $stSql .= "     to_char(max(dt_inicio),'dd/mm/yyyy') as dtinicio    ";
    $stSql .= " FROM                                                    ";
    $stSql .= "     ".$this->getTabela()."                              ";

    return $stSql;

}

/**
    * Seleciona o campos referentes a data atual
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaVigenciasValidas(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSQL = $this->montaRecuperaVigenciasValidas();
    $this->setDebug( $stSQL );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );

    return $obErro;
}

/**
    * Monta consulta para recuperar as vigencias validas
    * @access Private
    * @return String $stSQL
*/
function montaRecuperaVigenciasValidas()
{
    $stSQL .= " SELECT                                                              \n";
    $stSQL .= "     cod_vigencia,                                                   \n";
    $stSQL .= "     to_char(dt_inicio,'dd/mm/yyyy') as dt_inicio,                   \n";
    $stSQL .= "     timestamp                                                       \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "     ".$this->getTabela()."                                          \n";
    $stSQL .= " WHERE                                                               \n";
    $stSQL .= "     dt_inicio <= now()                                              \n";
    $stSQL .= "     AND cod_vigencia > 0                                            \n";

    return $stSQL;
}

}
