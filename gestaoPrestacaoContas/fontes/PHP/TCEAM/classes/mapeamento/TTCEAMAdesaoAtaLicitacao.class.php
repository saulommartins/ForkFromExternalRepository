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
    * Data de Criação: 28/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/
class TTCEAMAdesaoAtaLicitacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMAdesaoAtaLicitacao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql = " SELECT cod_processo AS num_processo
                        , ata.num_ata
                        , licitacao.cod_licitacao AS processo_licitatorio
                        , TO_CHAR(publicacao_ata.dt_publicacao, 'ddmmyyyy') AS dt_publicacao
                        , TO_CHAR(ata.dt_validade_ata, 'ddmmyyyy') AS dt_validade
                        , veiculos_publicidade.numcgm AS num_diario
                        , TO_CHAR(ata.timestamp, 'ddmmyyyy') AS dt_adesao
                        , tipo_adesao_ata.codigo AS tipo_adesao
                        
                     FROM licitacao.ata
                     
                     JOIN licitacao.edital
                       ON edital.num_edital = ata.num_edital
                      AND edital.exercicio = ata.exercicio
                     
                     JOIN licitacao.licitacao
                       ON licitacao.cod_licitacao = edital.cod_licitacao
                      AND licitacao.cod_modalidade = edital.cod_modalidade
                      AND licitacao.cod_entidade = edital.cod_entidade
                      AND licitacao.exercicio = edital.exercicio_licitacao
                     
                     JOIN licitacao.publicacao_ata
                       ON publicacao_ata.ata_id = ata.id
                     
                     JOIN licitacao.veiculos_publicidade
                       ON veiculos_publicidade.numcgm = publicacao_ata.numcgm
                     
                     JOIN licitacao.tipo_adesao_ata
                       ON tipo_adesao_ata.codigo = ata.tipo_adesao
                       
                    WHERE ata.exercicio = '".$this->getDado('exercicio')."'
                      AND TO_CHAR(ata.timestamp, 'mm') = '".$this->getDado('mes')."'
                      AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
        ";
        
        return $stSql;
    }
}
?>
