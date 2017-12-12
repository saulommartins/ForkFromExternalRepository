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
    * Data de Criação: 19/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: TFrotaMotorista.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaMotorista extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TFrotaMotorista()
    {
        parent::Persistente();
        $this->setTabela('frota.motorista');
        $this->setCampoCod('cgm_motorista');
        $this->setComplementoChave('');
        $this->AddCampo('cgm_motorista' ,'integer',true,'',true,true);
        $this->AddCampo('ativo'         ,'boolean',true,'',false,false);
    }

    public function recuperaMotorista(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaMotorista",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaMotorista()
    {
        $stSql = "
            SELECT sw_cgm_pessoa_fisica.cod_categoria_cnh
                 , sw_cgm_pessoa_fisica.num_cnh
                 , TO_CHAR(sw_cgm_pessoa_fisica.dt_validade_cnh,'dd/mm/yyyy') AS dt_validade_cnh
              FROM sw_cgm_pessoa_fisica
         LEFT JOIN sw_categoria_habilitacao
                ON sw_categoria_habilitacao.cod_categoria = sw_cgm_pessoa_fisica.cod_categoria_cnh
             WHERE ";
        if ( $this->getDado('cgm_motorista') != '' ) {
            $stSql .= " sw_cgm_pessoa_fisica.numcgm = ".$this->getDado('cgm_motorista')." AND   ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaMotoristaAnalitico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMotoristaAnalitico",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaMotoristaAnalitico()
    {
        $stSql = "
            SELECT motorista.cgm_motorista
                 , motorista.ativo
                 , sw_cgm.nom_cgm AS nom_motorista
                 , sw_cgm_pessoa_fisica.cod_categoria_cnh
                 , sw_cgm_pessoa_fisica.num_cnh
                 , TO_CHAR( sw_cgm_pessoa_fisica.dt_validade_cnh, 'dd/mm/yyyy' ) AS dt_validade_cnh
              FROM frota.motorista
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = cgm_motorista
        INNER JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
             WHERE ";
        if ( $this->getDado( 'cgm_motorista' ) != '' ) {
            $stSql .= " motorista.cgm_motorista = ".$this->getDado( 'cgm_motorista' )." AND   ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaMotoristaSintetico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMotoristaSintetico",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaMotoristaSintetico()
    {
        $stSql = "
            SELECT motorista.cgm_motorista
                 , sw_cgm.nom_cgm AS nom_motorista
                 , motorista.ativo
                 , CASE WHEN motorista.ativo IS TRUE
                        THEN 'Ativo'
                        ELSE 'Inativo'
                   END AS ativo_desc
              FROM frota.motorista
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = motorista.cgm_motorista
        INNER JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
        ";

        return $stSql;
    }
}
