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
    * Classe de mapeamento da tabela TESOURARIA_TRANSFERENCIA_ESTORNADA
    * Data de Criação: 21/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTesourariaTransferenciaEstornada.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-09-18 07:47:57 -0300 (Seg, 18 Set 2006) $

    * Casos de uso: uc-02.04.04, uc-02.04.26, uc-02.04.27
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_TRANSFERENCIA_ESTORNADA
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaTransferenciaEstornada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaTransferenciaEstornada()
{
    parent::Persistente();
    $this->setTabela("tesouraria.transferencia_estornada");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote_estorno,exercicio,cod_entidade,tipo');

    $this->AddCampo('cod_lote_estorno'       , 'integer'  , true , ''  , true  , true  );
    $this->AddCampo('exercicio'              , 'varchar'  , true , '04', true  , true  );
    $this->AddCampo('cod_entidade'           , 'integer'  , true , ''  , true  , true  );
    $this->AddCampo('tipo'                   , 'char'     , true , '01', true  , true  );
    $this->AddCampo('cod_autenticacao'       , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('dt_autenticacao'        , 'date'     , true , ''  , false , true  );
    $this->AddCampo('cod_lote'               , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('cod_boletim'            , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('cod_historico'          , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('cod_terminal'           , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('timestamp_terminal'     , 'timestamp', true , ''  , false , true  );
    $this->AddCampo('cgm_usuario'            , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('timestamp_usuario'      , 'timestamp', true , ''  , false , true  );
    $this->AddCampo('timestamp_estornada'    , 'timestamp', false, ''  , false , false );
    $this->AddCampo('observacao'             , 'text'     , false, ''  , false , false );
    $this->AddCampo('valor'                  , 'numeric'  , true,  '14,2'  , false , false );

}

function montaVerificaValorEstornado()
{
     $stSql  = "   SELECT coalesce(sum(te.valor),0.00) as valor                         \n";
     $stSql .= "   FROM tesouraria.transferencia_estornada as te,        \n";
     $stSql .= "        tesouraria.transferencia           as tr        \n";
     $stSql .= "   WHERE                                                    \n";
     $stSql .= "        tr.cod_lote        = te.cod_lote          AND        \n";
     $stSql .= "        tr.cod_entidade    = te.cod_entidade      AND        \n";
     $stSql .= "        tr.exercicio       = te.exercicio         AND         \n";
     $stSql .= "        tr.tipo            = te.tipo                         \n";
if($this->getDado('cod_entidade'))
     $stSql .= "        AND te.cod_entidade = ".$this->getDado('cod_entidade')." ";
if($this->getDado('exercicio'))
     $stSql .= "        AND te.exercicio = '".$this->getDado('exercicio')."' ";
if($this->getDado('cod_lote'))
     $stSql .= "        AND te.cod_lote = ".$this->getDado('cod_lote')." ";
if($this->getDado('tipo'))
     $stSql .= "        AND te.tipo = '".$this->getDado('tipo')."' ";

    return $stSql;
}

function verificaValorEstornado(&$nuValor, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaValorEstornado();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $nuValor = $rsRecordSet->getCampo('valor');

    return $obErro;
}

function recuperaTransferenciaEstornada(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera('montaRecuperaTransferenciaEstornada', $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaTransferenciaEstornada()
{
    $stSql = "
        SELECT TO_CHAR(transferencia_estornada.timestamp_estornada,'dd/mm/YYYY') AS dt_estorno
             , valor
          FROM tesouraria.transferencia_estornada
    ";

    if ($this->getDado('exercicio')) {
        $stFiltro .= " AND transferencia_estornada.exercicio = '".$this->getDado("exercicio")."' \n";
    }
    if ($this->getDado('cod_lote')) {
        $stFiltro .= " AND transferencia_estornada.cod_lote = ".$this->getDado("cod_lote")." \n";
    }
    if ($this->getDado('cod_entidade')) {
        $stFiltro .= " AND transferencia_estornada.cod_entidade IN (".$this->getDado("cod_entidade").") \n";
    }
    if ($this->getDado('tipo')) {
        $stFiltro .= " AND transferencia_estornada.tipo = '".$this->getDado('tipo')."' \n";
    }

    return ($stFiltro) ? $stSql . ' WHERE ' . substr($stFiltro,4) : $stSql;
}

}
