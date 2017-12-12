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
    * Classe de mapeamento da tabela TESOURARIA_AUTENTICACAO
    * Data de Criação: 26/12/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTesourariaAutenticacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.15
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_AUTENTICAÇÃO
  * Data de Criação: 26/12/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaAutenticacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaAutenticacao()
{
    parent::Persistente();
    $this->setTabela("tesouraria.autenticacao");

    $this->setCampoCod('cod_autenticacao');
    $this->setComplementoChave('dt_autenticacao');

    $this->AddCampo('cod_autenticacao', 'integer', true, '' , true , false );
    $this->AddCampo('dt_autenticacao' , 'date'   , true, '' , true , false );
    $this->AddCampo('tipo'            , 'char'   , true, '1', false, false );

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  String  $inCodAutenticacao
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function consultaMovimentacao(&$boMovimentacao, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaConsultaMovimentacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $boMovimentacao = $rsRecordSet->getCampo("movimentacao");

    return $obErro;
}

function montaConsultaMovimentacao()
{
    $stSql  = "SELECT                                                                                                       \n";
    $stSql .= "    case when count(cod_autenticacao) > 0 then true else false end as movimentacao                           \n";
    $stSql .= "FROM                                                                                                         \n";
    $stSql .= "    tesouraria.autenticacao                                                                                  \n";
    $stSql .= $this->getDado('filtro');

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  String  $inCodAutenticacao
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function proximoCodigo(&$inCodAutenticacao, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->buscaProximoCodigo();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $inCodAutenticacao = $rsRecordSet->getCampo("cod_autenticacao");

    return $obErro;
}

function buscaProximoCodigo()
{
    $stSql  = "SELECT                                                                                                       \n";
    $stSql .= "    (coalesce(max(cod_autenticacao),0) + 1) as cod_autenticacao                                              \n";
    $stSql .= "FROM                                                                                                         \n";
    $stSql .= "    tesouraria.autenticacao                                                                                  \n";
    $stSql .= "WHERE                                                                                                        \n";

    if ($this->getDado('numeracao_comprovacao') == 1) {
        $stSql .= "    --DIA                                                                                                    \n";
        $stSql .= "    to_char(dt_autenticacao,'dd/mm/yyyy') = '".$this->getDado('dt_autenticacao')."'                          \n";
    } else {
        if ($this->getDado('reiniciar_comprovacao') != "true") {
            $stSql .= "    --EXERCÍCIO ANTERIOR                                                                                                                 \n";
            $stSql .= "    2 = (                                                                                                                                \n";
            $stSql .= "        select                                                                                                                           \n";
            $stSql .= "            count(exercicio)                                                                                                             \n";
            $stSql .= "        from                                                                                                                             \n";
            $stSql .= "            administracao.configuracao                                                                                                   \n";
            $stSql .= "        where                                                                                                                            \n";
            $stSql .= "            exercicio = cast(cast(substr('".$this->getDado('dt_autenticacao')."',7,4) as integer) - 1 as varchar) and                    \n";
            $stSql .= "            (parametro = 'numeracao_comprovacao' and valor='2') or                                                                       \n";
            $stSql .= "            (parametro = 'reiniciar_comprovacao' and valor!='true')                                                                      \n";
            $stSql .= "    ) AND to_char(dt_autenticacao,'yyyy') = cast(cast(substr('".$this->getDado('dt_autenticacao')."',7,4) as integer) - 1 as varchar)    \n";
        } else {
            $stSql .= "    --EXERCICIO                                                                                              \n";
            $stSql .= "    to_char(dt_autenticacao,'yyyy') = substr('".$this->getDado('dt_autenticacao')."',7,4)                    \n";
        }
    }

    return $stSql;
}

}
