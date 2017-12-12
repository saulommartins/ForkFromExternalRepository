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
    * Extensão da Classe de mapeamento
    * Data de Criação: 08/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63482 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.2  2007/10/03 02:50:44  diego
Corrigindo formatação

Revision 1.1  2007/06/22 22:50:29  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

/**
  *
  * Data de Criação: 05/02/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCMBAContaCont extends TContabilidadePlanoConta
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMBAContaCont()
{
    parent::TContabilidadePlanoConta();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= "
       SELECT pc.exercicio::VARCHAR||LPAD(".$this->getDado('inMes')."::VARCHAR,2,'0') AS competencia
            , pc.exercicio AS dt_ano_criacao
            , 1 AS tipo_registro
            , '".$this->getDado('inCodUnidadeGestora')."' AS unidade_gestora
            , '' AS reservado_tcm
            , pc.cod_estrutural AS cd_conta_contabil
            , CASE WHEN pb.cod_banco IS NULL
                      THEN
                          CASE WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '1%'
                                    THEN
                                        CASE WHEN pc.cod_estrutural = '1.1.1.1.1.02.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.1.1.1.03.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.1.1.1.04.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.1.1.1.05.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.1.1.1.06.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.1.1.1.19.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.1.1.1.20.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.1.1.1.50.00.00.00.00' THEN 1
                                             WHEN pc.cod_estrutural = '1.1.4.0.0.00.00.00.00.00' THEN 1
                                          ELSE
                                              5
                                        END
                               WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '5%' THEN 2
                               WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '6%' THEN 3
                               WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '8%' THEN 4
                               WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '2%' THEN 6
                               WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '3%' THEN 7
                               WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '4%' THEN 8
                               WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) LIKE '7%' THEN 0
                          END
                    ELSE
                        1
              END AS tp_conta_contabil
            , row_number() over (order by pc.cod_estrutural ) AS nu_sequencial_tc
            , CASE WHEN pb.cod_banco IS NOT NULL
                   THEN pr.cod_recurso
                   ELSE 0
               END AS tcd_fonte_gestor
            , remove_acentos(pc.nom_conta) AS nm_conta_contabil
            , 'S' AS st_conta_ativa
            , CASE pc.natureza_saldo
                   WHEN 'devedor' THEN 'D'
                   WHEN 'credor'  THEN 'C'
                   WHEN 'misto'   THEN 'M'
                   ELSE ''
               END AS tp_origem_saldo
            , CASE WHEN contabilidade.fn_tipo_conta_plano(pc.exercicio, pc.cod_estrutural) = 'A'
                   THEN 1
                   ELSE 2
               END AS cd_recebe_lancamento
            , conta_bancarias.num_banco AS cd_banco
            , conta_bancarias.num_agencia AS cd_agencia_bancaria
            , conta_bancarias.num_conta_corrente AS cd_conta_bancaria
         FROM contabilidade.plano_conta AS pc
    LEFT JOIN contabilidade.plano_analitica AS pa
           ON pc.exercicio = pa.exercicio
          AND pc.cod_conta = pa.cod_conta
    LEFT JOIN contabilidade.plano_recurso AS pr
           ON pr.exercicio = pa.exercicio
          AND pr.cod_plano = pa.cod_plano
    LEFT JOIN contabilidade.plano_banco AS pb
           ON pa.exercicio = pb.exercicio
          AND pa.cod_plano = pb.cod_plano
    LEFT JOIN (SELECT conta_corrente.cod_banco
                    , conta_corrente.cod_agencia
                    , conta_corrente.cod_conta_corrente
                    , banco.num_banco
                    , agencia.num_agencia
                    , conta_corrente.num_conta_corrente
                 FROM monetario.banco
           INNER JOIN monetario.agencia
                   ON agencia.cod_banco = banco.cod_banco
           INNER JOIN monetario.conta_corrente
                   ON conta_corrente.cod_banco = agencia.cod_banco
                  AND conta_corrente.cod_agencia = agencia.cod_agencia
              ) AS conta_bancarias
           ON conta_bancarias.cod_banco = pb.cod_banco
          AND conta_bancarias.cod_agencia = pb.cod_agencia
          AND conta_bancarias.cod_conta_corrente = pb.cod_conta_corrente
        WHERE pc.exercicio = '".$this->getDado('exercicio')."'
     ORDER BY pc.exercicio
            , pc.cod_estrutural
    ";
    return $stSql;
}

}
