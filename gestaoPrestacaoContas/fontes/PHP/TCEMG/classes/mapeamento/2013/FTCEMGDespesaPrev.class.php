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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class FTCEMGDespesaPrev extends Persistente
{
    public function FTCEMGDespesaPrev()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_despesa_prev');

        $this->AddCampo('exercicio'    , 'varchar' , false , '' , false , false );
        $this->AddCampo('cod_entidade' , 'varchar' , false , '' , false , false );
        $this->AddCampo('dt_inicial'   , 'integer' , false , '' , false , false );
        $this->AddCampo('dt_final'     , 'integer' , false , '' , false , false );
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "

            SELECT 
                    ".$this->getDado('bimestre')." as bimestre
                    ,codtipo
                    ,REPLACE(despAdmGeral            ::VARCHAR,'.','') as despAdmGeral
                    ,REPLACE(despPrevSoci            ::VARCHAR,'.','') as despPrevSoci
                    ,REPLACE(despPrevSocInatPens     ::VARCHAR,'.','') as despPrevSocInatPens
                    ,REPLACE(outrasDespCorrentes     ::VARCHAR,'.','') as outrasDespCorrentes
                    ,REPLACE(despInvestimentos       ::VARCHAR,'.','') as despInvestimentos
                    ,REPLACE(despInversoesFinanceiras::VARCHAR,'.','') as despInversoesFinanceiras
                    ,REPLACE(despesasPrevIntra       ::VARCHAR,'.','') as despesasPrevIntra
                    ,REPLACE(despReserva             ::VARCHAR,'.','') as despReserva
                    ,REPLACE(despOutrasReservas      ::VARCHAR,'.','') as despOutrasReservas
                    ,REPLACE(despCorrentes           ::VARCHAR,'.','') as despCorrentes
                    ,REPLACE(despCapital             ::VARCHAR,'.','') as despCapital
                    ,REPLACE(outrosBeneficios        ::VARCHAR,'.','') as outrosBeneficios
                    ,REPLACE(contPrevidenciaria      ::VARCHAR,'.','') as contPrevidenciaria
                    ,REPLACE(outrasDespesas          ::VARCHAR,'.','') as outrasDespesas
                FROM ".$this->getTabela()."('".$this->getDado('exercicio')."','".$this->getDado('cod_entidade')."','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."') as
                retorno (   
                            codtipo                   INTEGER,
                            despAdmGeral               NUMERIC,
                            despPrevSoci               NUMERIC,
                            despPrevSocInatPens        NUMERIC,
                            outrasDespCorrentes        NUMERIC, 
                            despInvestimentos          NUMERIC,
                            despInversoesFinanceiras   NUMERIC,
                            despesasPrevIntra          NUMERIC,
                            despReserva                NUMERIC,
                            despOutrasReservas         NUMERIC,
                            despCorrentes              NUMERIC,
                            despCapital                NUMERIC,
                            outrosBeneficios           NUMERIC,
                            contPrevidenciaria         NUMERIC,
                            outrasDespesas             NUMERIC
                    )
            ORDER BY codtipo

        ";
        return $stSql;
    }
}
?>
