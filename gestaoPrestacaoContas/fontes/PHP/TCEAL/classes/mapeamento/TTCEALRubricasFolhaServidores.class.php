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

    * Extensão da Classe de Mapeamento TTCEALRubricasFolhaServidores
    *
    * Data de Criação: 28/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: TTCEALRubricasFolhaServidores.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @ignore
    *
*/
class TTCEALRubricasFolhaServidores extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALRubricasFolhaServidores()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    public function listarExportacaoRubricasFolhaServidores(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {

        $stSql = "
                            SELECT (SELECT sw_cgm_pessoa_juridica.cnpj
                                            FROM sw_cgm_pessoa_juridica
                                              JOIN sw_cgm
                                                ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                                              JOIN orcamento.entidade
                                                ON entidade.numcgm = sw_cgm.numcgm
                                         WHERE entidade.cod_entidade =  ".$this->getDado('inCodEntidade')."
                                             AND entidade.exercicio = '".$this->getDado('stExercicio')."'
                                           ) AS cod_und_gestora
                                        , (SELECT CASE WHEN configuracao_entidade.valor <> '' THEN valor ELSE '0000' END AS valor
                                              FROM administracao.configuracao_entidade
                                            WHERE configuracao_entidade.cod_modulo = 62
                                                 AND configuracao_entidade.exercicio = '".$this->getDado('stExercicio')."'
                                                 AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                                 AND configuracao_entidade.cod_entidade =  ".$this->getDado('inCodEntidade')."
                                        ) AS codigo_ua
                                        , codigo as cod_rubrica_sal
                                        , descricao
                                        , CASE WHEN natureza = 'P' THEN 1
                                               WHEN natureza = 'D' THEN 2
                                          END AS cod_tipo_rubrica
                                FROM folhapagamento".$this->getDado('stEntidade').".evento
                              WHERE natureza = 'P' OR natureza='D';


                    ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }
}
?>
