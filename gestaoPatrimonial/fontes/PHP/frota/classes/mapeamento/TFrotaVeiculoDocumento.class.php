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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: TFrotaVeiculoDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaVeiculoDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaVeiculoDocumento()
    {
        parent::Persistente();
        $this->setTabela('frota.veiculo_documento');
        $this->setCampoCod('cod_veiculo');
        $this->setComplementoChave('cod_documento,exercicio');

        $this->AddCampo('cod_documento','integer',true,'',true,true);
        $this->AddCampo('cod_veiculo','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('mes','integer',true,'',false,false);
    }

    public function recuperaDocumentos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDocumentos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaDocumentos()
    {
        $stSql = "
            SELECT veiculo_documento.cod_veiculo
                 , veiculo_documento.cod_documento
                 , documento.nom_documento
                 , veiculo_documento.exercicio
                 , veiculo_documento.mes
                 , CASE WHEN ( veiculo_documento_empenho.cod_empenho IS NULL )
                        THEN 'naopago'
                        ELSE 'pago'
                   END AS situacao
                 , veiculo_documento_empenho.cod_empenho
                 , beneficiario.nom_cgm AS nom_empenho
                 , veiculo_documento_empenho.cod_entidade
                 , sw_entidade.nom_cgm AS nom_entidade
                 , veiculo_documento_empenho.exercicio_empenho
              FROM frota.veiculo_documento
        INNER JOIN frota.documento
                ON documento.cod_documento = veiculo_documento.cod_documento
         LEFT JOIN frota.veiculo_documento_empenho
                ON veiculo_documento_empenho.exercicio = veiculo_documento.exercicio
               AND veiculo_documento_empenho.cod_documento = veiculo_documento.cod_documento
               AND veiculo_documento_empenho.cod_veiculo = veiculo_documento.cod_veiculo
         LEFT JOIN empenho.empenho
                ON empenho.cod_empenho = veiculo_documento_empenho.cod_empenho
               AND empenho.cod_entidade = veiculo_documento_empenho.cod_entidade
               AND empenho.exercicio = veiculo_documento_empenho.exercicio_empenho
         LEFT JOIN empenho.pre_empenho
                ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               AND pre_empenho.exercicio = empenho.exercicio
         LEFT JOIN sw_cgm AS beneficiario
                ON beneficiario.numcgm = pre_empenho.cgm_beneficiario
         LEFT JOIN orcamento.entidade
                ON entidade.cod_entidade = veiculo_documento_empenho.cod_entidade
               AND entidade.exercicio = veiculo_documento_empenho.exercicio_empenho
         LEFT JOIN sw_cgm AS sw_entidade
                ON sw_entidade.numcgm = entidade.numcgm
             WHERE ";
        if ( $this->getDado('cod_veiculo') ) {
            $stSql .= " veiculo_documento.cod_veiculo = ".$this->getDado('cod_veiculo')." AND   ";
        }

        return substr($stSql,0,-6);
    }

}
