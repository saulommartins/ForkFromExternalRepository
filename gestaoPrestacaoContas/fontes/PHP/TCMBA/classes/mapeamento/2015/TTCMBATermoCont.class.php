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
    * Data de Criação: 22/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63087 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/07/22 20:21:25  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBATermoCont extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
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
    $stSql = "
                SELECT 1 AS tipo_registro
                     , ".$this->getDado('inCodUnidadeGestora')." AS unidade_gestora
                     , contrato.numero_contrato
                     , contrato.numero_contrato AS num_termo
                     , pessoa_fisica_juridica.documento
                     , pessoa_fisica_juridica.tipo_pessoa
                     , contrato.dt_assinatura AS dt_termo
                     , sw_cgm.nom_cgm AS nom_responsavel
                     , publicacao_contrato.dt_publicacao
                     , contrato.valor_contratado AS vl_termo
                     , TO_CHAR(contrato.dt_assinatura,'yyyymm') AS competencia
                     , contrato.justificativa
                     , contrato.fundamentacao_legal
                     , contrato.objeto
                FROM licitacao.contrato
                INNER JOIN licitacao.contrato_licitacao
                        ON contrato_licitacao.num_contrato = contrato.num_contrato
                       AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                       AND contrato_licitacao.exercicio = contrato.exercicio
                INNER JOIN licitacao.licitacao
                        ON licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
                       AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                       AND licitacao.cod_entidade = contrato_licitacao.cod_entidade
                       AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao
                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = contrato.cgm_contratado
                INNER JOIN ( SELECT numcgm
                                  , cnpj as documento
                                  , 2 AS tipo_pessoa
                               FROM sw_cgm_pessoa_juridica

                              UNION

                               SELECT numcgm
                                  , cpf as documento
                                  , 1 AS tipo_pessoa
                               FROM sw_cgm_pessoa_fisica 
                     ) AS pessoa_fisica_juridica        
                     ON pessoa_fisica_juridica.numcgm = contrato.cgm_contratado
                INNER JOIN licitacao.publicacao_contrato
                        ON publicacao_contrato.num_contrato = contrato.num_contrato
                       AND publicacao_contrato.cod_entidade = contrato.cod_entidade
                       AND publicacao_contrato.exercicio = contrato.exercicio
                INNER JOIN licitacao.justificativa_razao
                        ON justificativa_razao.cod_licitacao = licitacao.cod_licitacao
                       AND justificativa_razao.cod_entidade = licitacao.cod_entidade
                       AND justificativa_razao.exercicio = licitacao.exercicio
                       AND justificativa_razao.cod_modalidade = licitacao.cod_modalidade
                INNER JOIN licitacao.cotacao_licitacao
                        ON cotacao_licitacao.cod_licitacao = licitacao.cod_licitacao
                       AND cotacao_licitacao.cod_modalidade = licitacao.cod_modalidade
                       AND cotacao_licitacao.cod_entidade = licitacao.cod_entidade
                       AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
                WHERE contrato.dt_assinatura BETWEEN TO_DATE('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('stDataFinal')."','dd/mm/yyyy')
                  AND contrato.cod_entidade IN (".$this->getDado('stEntidades').")
                  AND contrato.exercicio = '".$this->getDado('exercicio')."'

                GROUP BY tipo_registro
                       , unidade_gestora
                       , contrato.numero_contrato
                       , num_termo
                       , pessoa_fisica_juridica.documento
                       , pessoa_fisica_juridica.tipo_pessoa
                       , dt_termo
                       , nom_responsavel
                       , publicacao_contrato.dt_publicacao
                       , vl_termo
                       , competencia
                       , contrato.justificativa
                       , contrato.fundamentacao_legal
                       , contrato.objeto
            ";
    return $stSql;
}

}
