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
    * Extensão da Classe de mapeamento Arquivo: EditalCadastro.txt
    * Data de Criação: 02/09/2015

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: $
    $Name$
    $Author: $
    $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBAEditalCadastro extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
    }

    function recuperaEditalCadastro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEditalCadastro().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaEditalCadastro()
    {
        $stSql = "  SELECT 1 AS tipo_registro
                         , ".$this->getDado('inCodGestora')." AS unidade_gestora
                         , edital.num_edital
                         , CASE WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 1 THEN 1
                                WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 2 THEN 2
                                WHEN modalidade.cod_modalidade = 3 AND licitacao.registro_precos = TRUE THEN 3
                                WHEN modalidade.cod_modalidade = 5 THEN 4
                                WHEN modalidade.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 1 THEN 5
                                WHEN modalidade.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 2 THEN 6
                                WHEN modalidade.cod_modalidade = 4 THEN 7
                                WHEN modalidade.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 1 THEN 10
                                WHEN modalidade.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 2 THEN 12
                                WHEN modalidade.cod_modalidade = 6 AND licitacao.registro_precos = FALSE THEN 14
                                WHEN modalidade.cod_modalidade = 7 AND licitacao.registro_precos = FALSE THEN 15
                                WHEN modalidade.cod_modalidade = 1 AND licitacao.registro_precos = TRUE THEN 16
                                WHEN modalidade.cod_modalidade = 2 AND licitacao.registro_precos = TRUE THEN 17
                                WHEN modalidade.cod_modalidade = 6 AND licitacao.registro_precos = TRUE THEN 18
                                WHEN modalidade.cod_modalidade = 7 AND licitacao.registro_precos = TRUE THEN 19
                                WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 4 THEN 22
                                WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 3 THEN 23
                           END AS edital_modalidade
                         , LPAD(licitacao.cod_processo::VARCHAR,8,'0')||licitacao.exercicio_processo AS cod_administrativo
                         , TO_CHAR(publicacao_edital.data_publicacao, 'DDMMYYYY') AS data_publicacao
                         , objeto.descricao AS objeto
                         , edital.observacao_validade_proposta AS observacao_objeto
                         , TO_CHAR(edital.dt_abertura_propostas, 'DDMMYYYY') AS dt_abertura_propostas
                         , REPLACE(edital.hora_abertura_propostas, ':', '') AS hora_abertura_propostas
                         , ".$this->getDado('stExercicio').$this->getDado('inMes')." AS competencia
            
                      FROM licitacao.edital
            
                INNER JOIN licitacao.publicacao_edital
                        ON publicacao_edital.num_edital = edital.num_edital
                       AND publicacao_edital.exercicio  = edital.exercicio
            
                INNER JOIN licitacao.licitacao
                        ON licitacao.cod_licitacao  = edital.cod_licitacao
                       AND licitacao.cod_modalidade = edital.cod_modalidade
                       AND licitacao.cod_entidade   = edital.cod_entidade   
                       AND licitacao.exercicio      = edital.exercicio_licitacao 

                INNER JOIN licitacao.homologacao
                        ON homologacao.cod_licitacao  = licitacao.cod_licitacao
                       AND homologacao.cod_modalidade = licitacao.cod_modalidade
                       AND homologacao.cod_entidade   = licitacao.cod_entidade
                       AND homologacao.exercicio_licitacao = licitacao.exercicio
            
                INNER JOIN compras.objeto
                        ON objeto.cod_objeto = licitacao.cod_objeto
            
                INNER JOIN compras.tipo_objeto
                        ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
            
                INNER JOIN compras.modalidade
                        ON modalidade.cod_modalidade = licitacao.cod_modalidade
            
                     WHERE edital.exercicio = '".$this->getDado('stExercicio')."'
                       AND edital.cod_entidade IN (".$this->getDado('stEntidade').")
                       AND modalidade.cod_modalidade NOT IN (9,8)
                       AND edital.dt_abertura_propostas BETWEEN TO_DATE('".$this->getDado('dtInicio')."', 'DD/MM/YYYY')
                                                            AND TO_DATE('".$this->getDado('dtFim')."', 'DD/MM/YYYY')
                  
                  GROUP BY edital.num_edital
                         , edital_modalidade
                         , cod_administrativo
                         , data_publicacao
                         , objeto
                         , observacao_objeto
                         , dt_abertura_propostas
                         , hora_abertura_propostas ";
        return $stSql;
    }

    public function __destruct() {}
}

?>