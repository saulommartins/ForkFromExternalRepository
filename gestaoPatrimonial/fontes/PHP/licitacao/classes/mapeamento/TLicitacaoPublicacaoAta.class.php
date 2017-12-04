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
    * Classe de mapeamento da tabela licitacao.publicacao_contrato
    * Data de Criação: 12/10/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Rodrigo

    * $Id: TLicitacaoPublicacaoAta.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoPublicacaoAta extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TLicitacaoPublicacaoAta()
    {
        parent::Persistente();
        $this->setTabela("licitacao.publicacao_ata");
        
        $this->setComplementoChave('id,ata_id');
        
        $this->AddCampo('id','integer',true,'',true,false);
        $this->AddCampo('ata_id','integer',true,'',false,true);
        $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('dt_publicacao','date',true,'',false,false);
        $this->AddCampo('observacao','varchar',true,'80',false,false);
        $this->AddCampo('num_publicacao','integer',false,'',false,false);
        
    }

    public function recuperaVeiculosPublicacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculosPublicacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVeiculosPublicacao()
    {
        $stSql = "
                SELECT
                        publicacao_ata.id
                      , publicacao_ata.ata_id
                      , sw_cgm.nom_cgm AS nom_veiculo
                      , veiculos_publicidade.numcgm AS num_veiculo
                      , TO_CHAR(publicacao_ata.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao
                      , publicacao_ata.num_publicacao
                      , publicacao_ata.observacao
                      
                  FROM licitacao.publicacao_ata
                  
                  JOIN licitacao.veiculos_publicidade
                    ON veiculos_publicidade.numcgm = publicacao_ata.numcgm
                    
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = veiculos_publicidade.numcgm
                    
                 WHERE publicacao_ata.ata_id = '".$this->getDado('ata_id')."'
        ";
        return $stSql;
    }

}
