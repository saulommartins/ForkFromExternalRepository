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

  * $Id: TFrotaUtilizacaoRetorno.class.php 59777 2014-09-10 18:03:57Z jean $

    Caso de uso: uc-03.02.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TFrotaUtilizacaoRetorno extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaUtilizacaoRetorno()
    {
        parent::Persistente();
        $this->setTabela('frota.utilizacao_retorno');
        $this->setCampoCod('cod_veiculo');
        $this->setComplementoChave('dt_saida,hr_saida');

        $this->AddCampo('cod_veiculo'    ,'integer' ,true, '',true,true  );
        $this->AddCampo('dt_saida'       ,'date'    ,true, '',true,true  );
        $this->AddCampo('hr_saida'       ,'varchar',true, '',true,true  );
        $this->AddCampo('dt_retorno'     ,'date'    ,true, '',false,false);
        $this->AddCampo('hr_retorno'     ,'fulltime',true, '',false,false);
        $this->AddCampo('km_retorno'     ,'float'   ,true, '',false,false);
        $this->AddCampo('cgm_motorista'  ,'integer' ,true, '',false,true );
        $this->AddCampo('observacao'     ,'text'    ,true, '',false,false);
        $this->AddCampo('virada_odometro','boolean' ,true, '',false,false);
        $this->AddCampo('qtde_horas_trabalhadas','numeric',true,'6,2',false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql = "
            SELECT *
              FROM frota.utilizacao_retorno ";

        return $stSql;
    }

    public function recuperaKmInicial(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaKmInicial",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaKmInicial()
    {
        $stSql = "
            SELECT veiculo.cod_veiculo
              FROM frota.veiculo
         LEFT JOIN frota.utilizacao ";

        return $stSql;
    }

    public function recuperaRetornoVeiculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRetornoVeiculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRetornoVeiculo()
    {
        $stSql = "
            SELECT veiculo.cod_veiculo
                 , CASE WHEN ( utilizacao_retorno.km_retorno IS NULL AND utilizacao.km_saida IS NULL )
                        THEN veiculo.km_inicial
                        WHEN ( utilizacao.km_saida IS NOT NULL AND utilizacao_retorno IS NULL )
                        THEN utilizacao.km_saida
                        ELSE utilizacao_retorno.km_retorno
                   END AS km_inicial
                 , utilizacao.hr_saida
                 , utilizacao.dt_saida_veiculo
                 , utilizacao.cgm_motorista AS num_motorista
                 , utilizacao.nom_motorista
                 , utilizacao.destino
              FROM frota.veiculo
         LEFT JOIN ( SELECT cod_veiculo
                          , TO_CHAR(dt_saida,'dd/mm/yyyy') AS dt_saida_veiculo
                          , hr_saida
                          , cgm_motorista
                          , sw_cgm.nom_cgm AS nom_motorista
                          , km_saida
                          , destino
                       FROM frota.utilizacao
                 INNER JOIN sw_cgm
                         ON sw_cgm.numcgm = cgm_motorista
                      WHERE utilizacao.cod_veiculo = ".$this->getDado('cod_veiculo')."
                   ORDER BY dt_saida DESC, hr_saida DESC
                      LIMIT 1
                   ) AS utilizacao
                ON utilizacao.cod_veiculo = ".$this->getDado('cod_veiculo')."
         LEFT JOIN ( SELECT cod_veiculo
                          , km_retorno
                       FROM frota.utilizacao_retorno
                      WHERE cod_veiculo = ".$this->getDado('cod_veiculo')."
                   ORDER BY dt_saida DESC, hr_saida DESC
                      LIMIT 1
                   ) AS utilizacao_retorno
                ON utilizacao_retorno.cod_veiculo = veiculo.cod_veiculo

             WHERE 1=1 ";

        if ($this->getDado('cod_veiculo')) {
            $stSql .= " AND veiculo.cod_veiculo = ".$this->getDado('cod_veiculo');
        }

        if ($this->getDado('dt_saida')) {
            $stSql .= " AND TO_DATE(utilizacao.dt_saida_veiculo,'yyyy-mm-dd') = TO_DATE('".$this->getDado('dt_saida')."', 'yyyy-mm-dd')";
        }

        if ($this->getDado('hr_saida')) {
            $stSql .= " AND utilizacao.hr_saida = '".$this->getDado('hr_saida')."'";
        }
        
        return $stSql;
    }

    public function recuperaVeiculoSemRetorno(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaVeiculoSemRetorno().$stFiltro.$stOrder;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaVeiculoSemRetorno()
    {
        $stSql = "
           SELECT  veiculo.cod_veiculo
                ,  veiculo.cod_marca
                ,  marca.nom_marca
                ,  veiculo.cod_modelo
                ,  modelo.nom_modelo
                ,  veiculo.prefixo
                ,  veiculo.placa
                ,  CASE WHEN TRIM(veiculo.placa) <> '' THEN
                     SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4)
                   END as placa_masc

                ,  tipo_veiculo.nom_tipo AS tipo_veiculo
                ,  veiculo.cod_categoria
                ,  sw_categoria_habilitacao.nom_categoria
                ,  TO_CHAR(utilizacao.dt_saida, 'DD/MM/YYYY') as dt_saida
                ,  utilizacao.hr_saida
                ,  CASE WHEN ( utilizacao_retorno.km_retorno IS NULL AND utilizacao.km_saida IS NULL )
                        THEN veiculo.km_inicial
                        WHEN ( utilizacao.km_saida IS NOT NULL AND utilizacao_retorno IS NULL )
                        THEN utilizacao.km_saida
                        ELSE utilizacao_retorno.km_retorno
                   END AS km_inicial
                ,  utilizacao.cgm_motorista AS num_motorista
                ,  sw_cgm.nom_cgm AS nom_motorista
                ,  utilizacao.destino
                ,  tipo_veiculo.controlar_horas_trabalhadas

            FROM  frota.veiculo

      INNER JOIN  frota.marca
              ON  marca.cod_marca = veiculo.cod_marca

      INNER JOIN  frota.modelo
              ON  modelo.cod_modelo = veiculo.cod_modelo
             AND  modelo.cod_marca = veiculo.cod_marca

      INNER JOIN  frota.tipo_veiculo
              ON  tipo_veiculo.cod_tipo = veiculo.cod_tipo_veiculo

      INNER JOIN  sw_categoria_habilitacao
              ON  sw_categoria_habilitacao.cod_categoria = veiculo.cod_categoria

      INNER JOIN  frota.utilizacao
              ON  utilizacao.cod_veiculo = veiculo.cod_veiculo

      INNER JOIN  sw_cgm
              ON  sw_cgm.numcgm = utilizacao.cgm_motorista

       LEFT JOIN  frota.utilizacao_retorno
              ON  utilizacao_retorno.cod_veiculo = utilizacao.cod_veiculo
             AND  utilizacao_retorno.dt_saida    = utilizacao.dt_saida
             AND  utilizacao_retorno.hr_saida    = utilizacao.hr_saida

           WHERE  1=1

             AND  utilizacao_retorno.dt_retorno IS NULL ";

        if ($this->getDado('cod_veiculo')) {
            $stSql .= " AND veiculo.cod_veiculo = ".$this->getDado('cod_veiculo');
        }

        if ($this->getDado('prefixo')) {
            $stSql .= " AND veiculo.prefixo = '".$this->getDado('prefixo');
        }

        if ($this->getDado('placa')) {
            $stSql .= " AND SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) ILIKE '%".$this->getDado('placa')."%'";
        }

        if ($this->getDado('dt_saida')) {
            $stSql .= " AND utilizacao.dt_saida = '".$this->getDado('dt_saida')."'";
        }

        if ($this->getDado('hr_saida')) {
            $stSql .= " AND utilizacao.hr_saida = '".$this->getDado('hr_saida')."'";
        }

        return $stSql;
    }
}
