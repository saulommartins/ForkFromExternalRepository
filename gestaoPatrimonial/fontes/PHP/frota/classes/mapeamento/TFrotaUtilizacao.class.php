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
  * Mapeamento da tabela frota.veiculo_combustivel
  * Data de criação : 09/11/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Programador: Henrique Boaventura

  * $Id: TFrotaUtilizacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-03.02.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaUtilizacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaUtilizacao()
    {
        parent::Persistente();
        $this->setTabela('frota.utilizacao');
        $this->setCampoCod('cod_veiculo');
        $this->setComplementoChave('dt_saida,hr_saida');

        $this->AddCampo('cod_veiculo'   ,'integer',true, '',true,true);
        $this->AddCampo('dt_saida'      ,'date'   ,true, '',true,true);
        $this->AddCampo('hr_saida'      ,'varchar',true, '',true,true);
        $this->AddCampo('km_saida'      ,'float'  ,true, '',false,false);
        $this->AddCampo('cgm_motorista' ,'integer',true, '',true,true);
        $this->AddCampo('destino'       ,'text'   ,true, '',false,false);
    }

    public function recuperaListaUtilizacaoVeiculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaListaUtilizacaoVeiculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaListaUtilizacaoVeiculo()
    {
        $stSql = "SELECT VEICULO.COD_VEICULO
                    , MARCA.NOM_MARCA
                    , SUBSTR(VEICULO.PLACA,1,3) || '-' || SUBSTR(VEICULO.PLACA,4,4) AS PLACA
                    , MODELO.NOM_MODELO
                    , UTILIZACAO.DT_SAIDA
                    , UTILIZACAO.HR_SAIDA
                    , TO_CHAR(UTILIZACAO.DT_SAIDA,'dd/mm/yyyy') AS DT_SAIDA
                    , UTILIZACAO.KM_SAIDA
                    , TO_CHAR(UTILIZACAO_RETORNO.DT_RETORNO,'dd/mm/yyyy') AS DT_RETORNO
                    , UTILIZACAO_RETORNO.HR_RETORNO
                    , UTILIZACAO_RETORNO.KM_RETORNO
                 FROM FROTA.VEICULO
           INNER JOIN FROTA.MARCA
                   ON ( VEICULO.COD_MARCA = MARCA.COD_MARCA)
           INNER JOIN FROTA.MODELO
                   ON ( VEICULO.COD_MODELO = MODELO.COD_MODELO
                        AND VEICULO.COD_MARCA = MODELO.COD_MARCA
                      )
           INNER JOIN FROTA.UTILIZACAO
                   ON ( VEICULO.COD_VEICULO = UTILIZACAO.COD_VEICULO)
           LEFT JOIN FROTA.UTILIZACAO_RETORNO
                   ON ( VEICULO.COD_VEICULO = UTILIZACAO_RETORNO.COD_VEICULO
                        AND UTILIZACAO.COD_VEICULO = UTILIZACAO_RETORNO.COD_VEICULO
                        AND UTILIZACAO.DT_SAIDA = UTILIZACAO_RETORNO.DT_SAIDA
                        AND UTILIZACAO.HR_SAIDA = UTILIZACAO_RETORNO.HR_SAIDA
                      ) ";

        return $stSql;
    }
}
