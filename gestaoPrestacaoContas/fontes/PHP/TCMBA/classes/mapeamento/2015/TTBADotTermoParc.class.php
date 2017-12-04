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
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU LICENCA.txt *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 05/10/2015

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Arthur Cruz

    $Id: TTBADotTermoParc.class.php 64109 2015-12-03 15:34:08Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBADotTermoParc extends Persistente
{

    public function __construct(){
        parent::Persistente();
    }

    public function recuperaDadosDotacaoParceria(&$rsRecordSet, $stCondicao =  '', $stOrdem =  '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montarRecuperaDadosDotacaoParceria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montarRecuperaDadosDotacaoParceria()
    {
	$stSql =  "
               SELECT 1 AS tipo_registro
                    , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                    , termo_parceria_prorrogacao.nro_termo_aditivo AS num_termo
                    , row_number() over( ORDER BY despesa.cod_despesa ) AS num_sequencial
                    , despesa.cod_despesa
                    , despesa.num_unidade AS unidade_orcamentaria
                    , termo_parceria_dotacao.exercicio AS ano
                    , orcamento.fn_consulta_tipo_pao(despesa.exercicio, despesa.num_pao) AS tipo_projeto
                    , acao.num_acao AS cod_projeto
                    , despesa.cod_recurso AS fonte_recurso
                    , despesa.cod_funcao
                    , despesa.cod_subfuncao
                    , programa.num_programa AS cod_programa
                    , despesa.num_orgao AS cod_orgao
                    , TO_CHAR(termo_parceria.dt_inicio, 'YYYYMM') AS competencia

                 FROM tcmba.termo_parceria_dotacao

           INNER JOIN orcamento.despesa
                   ON despesa.exercicio   = termo_parceria_dotacao.exercicio_despesa
                  AND despesa.cod_despesa = termo_parceria_dotacao.cod_despesa

           INNER JOIN orcamento.conta_despesa
                   ON despesa.exercicio = conta_despesa.exercicio
                  AND despesa.cod_conta = conta_despesa.cod_conta

           INNER JOIN orcamento.pao
                   ON pao.exercicio = despesa.exercicio
                  AND pao.num_pao   = despesa.num_pao

           INNER JOIN orcamento.pao_ppa_acao
                   ON pao_ppa_acao.exercicio = pao.exercicio
                  AND pao_ppa_acao.num_pao   = pao.num_pao

           INNER JOIN ppa.acao
                   ON acao.cod_acao = pao_ppa_acao.cod_acao

           INNER JOIN ppa.programa
                   ON programa.cod_programa = acao.cod_programa

           INNER JOIN tcmba.termo_parceria
                   ON termo_parceria.exercicio    = termo_parceria_dotacao.exercicio
                  AND termo_parceria.cod_entidade = termo_parceria_dotacao.cod_entidade
                  AND termo_parceria.nro_processo = termo_parceria_dotacao.nro_processo 

           INNER JOIN tcmba.termo_parceria_prorrogacao
                   ON termo_parceria_prorrogacao.exercicio    = termo_parceria.exercicio
                  AND termo_parceria_prorrogacao.cod_entidade = termo_parceria.cod_entidade 
                  AND termo_parceria_prorrogacao.nro_processo = termo_parceria.nro_processo 

                WHERE termo_parceria_dotacao.exercicio    = '".$this->getDado('exercicio')."'
                  AND termo_parceria_dotacao.cod_entidade IN (".$this->getDado('entidades').")
                  AND (    ( termo_parceria.dt_inicio  <= TO_DATE('".$this->getDado('dt_inicial')."', 'DD/MM/YYYY') OR ( termo_parceria.dt_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'DD/MM/YYYY') AND TO_DATE('".$this->getDado('dt_final')."', 'DD/MM/YYYY') ) )--DATA INICIO
                       AND ( termo_parceria.dt_termino >= TO_DATE('".$this->getDado('dt_final')."', 'DD/MM/YYYY') OR ( termo_parceria.dt_termino BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'DD/MM/YYYY') AND TO_DATE('".$this->getDado('dt_final')."', 'DD/MM/YYYY') ) )--DATA TERMINO
                      )

             ORDER BY despesa.cod_despesa ";

        return $stSql;
    }

}

?>