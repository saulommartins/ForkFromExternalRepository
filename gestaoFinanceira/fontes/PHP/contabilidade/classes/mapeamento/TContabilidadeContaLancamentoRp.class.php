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
    * Classe de mapeamento da tabela contabilidade.conta_lancamento_rp
    * Data de Criação: 28/12/2006

    * @author Analista: Cleisson Barbosa,
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-12 15:56:46 -0200 (Qua, 12 Dez 2007) $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  contabilidade.conta_lancamento_rp
  * Data de Criação: 28/12/2006

  * @author Analista: Cleisson Barbosa,
  * @author Desenvolvedor: Bruce Cruz de Sena

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeContaLancamentoRp extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeContaLancamentoRp()
{
    parent::Persistente();
    $this->setTabela("contabilidade.conta_lancamento_rp");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_tipo_conta');

    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,false);
    $this->AddCampo('cod_tipo_conta','integer',true,'',true,true);
    $this->AddCampo('cod_plano','integer',true,'',false,true);

}

function excluiTudo($stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql = " delete from contabilidade.conta_lancamento_rp ".$stFiltro;

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;

}

function verificacaoInsuficientesRP(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaVerificacaoInsuficientesRP().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificacaoInsuficientesRP()
{
    $stSql = "  SELECT * FROM (
                                SELECT retorno.*
                                     , buscaCodigoEstrutural(retorno.exercicio,retorno.cod_plano_debito) AS cod_estrutural

                                 FROM stn.fn_rel_rgf6_emp_liq_exercicio_recurso_entidade( ".$this->getDado('cod_entidade')."::varchar,
                                                                                          '".$this->getDado('exercicio')."',
                                                                                          '01/01/".$this->getDado('exercicio')."',
                                                                                          '31/12/".$this->getDado('exercicio')."',
                                                                                          '31/12/".$this->getDado('exercicio')."'
                                                                                        ) as retorno
                                                                                    ( cod_recurso integer,
                                                                                      nom_recurso varchar,
                                                                                      cod_entidade integer,
                                                                                      exercicio varchar,
                                                                                      cod_plano_debito varchar,
                                                                                      liquidados_nao_pagos numeric,
                                                                                      empenhados_nao_liquidados numeric
                                                                                    )
                            ) as tbl
                        WHERE cod_estrutural LIKE '6.2.2.1.3.03%' OR cod_estrutural LIKE '6.2.2.1.3.01%'
    ";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql = "select conta_lancamento_rp.exercicio
                   , conta_lancamento_rp.cod_entidade
                   , conta_lancamento_rp.cod_tipo_conta
                   , conta_lancamento_rp.cod_plano
                   , tipo_conta_lancamento_rp.descricao as descricao_tipo
                   , plano_conta.nom_conta as descricao_conta

                from contabilidade.conta_lancamento_rp
                join contabilidade.tipo_conta_lancamento_rp
                  on ( conta_lancamento_rp.cod_tipo_conta = tipo_conta_lancamento_rp.cod_tipo_conta )
                join contabilidade.plano_analitica
                  on ( plano_analitica.exercicio  = conta_lancamento_rp.exercicio
                 and   plano_analitica.cod_plano  = conta_lancamento_rp.cod_plano )
                join contabilidade.plano_conta
                  on (plano_conta.exercicio = plano_analitica.exercicio
                 and  plano_conta.cod_conta = plano_analitica.cod_conta )
            ";

    return $stSql;

}

}
