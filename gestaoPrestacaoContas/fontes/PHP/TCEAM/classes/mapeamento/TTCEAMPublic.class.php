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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 31/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/
class TTCEAMPublic extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMPublic()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
        SELECT 0 AS reservado_tc
              , processo_licitatorio
              , data_publicacao
              , 0 AS sequencial_publicacao
              , nome_veiculo_comunicacao
           FROM (
            SELECT CASE WHEN licitacao.cod_modalidade = 1  THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 2  THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 3  THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 4  THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 5  THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 6  THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 7  THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 8  THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 9  THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 10 THEN 'OT'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 11 THEN 'RP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                   END AS processo_licitatorio
                 , TO_CHAR(publicacao_edital.data_publicacao, 'yyyymmdd') AS data_publicacao
                 , sw_cgm.nom_cgm AS nome_veiculo_comunicacao
              FROM licitacao.licitacao
              JOIN licitacao.edital
                ON edital.cod_licitacao       = licitacao.cod_licitacao
               AND edital.cod_modalidade      = licitacao.cod_modalidade
               AND edital.cod_entidade        = licitacao.cod_entidade
               AND edital.exercicio_licitacao = licitacao.exercicio
              JOIN licitacao.publicacao_edital
                ON publicacao_edital.num_edital = edital.num_edital
               AND publicacao_edital.exercicio  = edital.exercicio
              JOIN sw_cgm
                ON sw_cgm.numcgm = publicacao_edital.numcgm
             WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'
               AND to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."'
               AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
            ) AS registros
     GROUP BY processo_licitatorio
            , data_publicacao
            , nome_veiculo_comunicacao
        ";

        return $stSql;
    }
}
?>
