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
    * Classe de mapeamento da tabela TCEMG.CONFIGURACAO_LEIS_PPA
    * Data de Criação: 15/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGConfiguracaoLOA extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGConfiguracaoLOA()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configuracao_loa');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('');

        $this->AddCampo('exercicio'                              , 'varchar',  true,  4,  true, false);
        $this->AddCampo('cod_norma'                              , 'integer', false, '', false,  true);
        $this->AddCampo('percentual_abertura_credito'            , 'numeric', false, '', false, false);
        $this->AddCampo('percentual_contratacao_credito'         , 'numeric', false, '', false, false);
        $this->AddCampo('percentual_contratacao_credito_receita' , 'numeric', false, '', false, false);

    }

    public function recuperaRegistro10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro10()
    {
        $stSql = "
        SELECT
                '10' AS tipoRegistro
             ,  norma.num_norma as nroLOA
             ,  TO_CHAR(norma.dt_assinatura, 'ddmmyyyy') as dataLOA
             ,  TO_CHAR(norma.dt_publicacao, 'ddmmyyyy') as dataPubLOA
             ,  1 AS discriDespLOA
          FROM  tcemg.configuracao_loa
    INNER JOIN  normas.norma
            ON  norma.cod_norma = configuracao_loa.cod_norma
         WHERE  configuracao_loa.exercicio = '".$this->getDado('exercicio')."'
        ";

        return $stSql;
    }

    public function recuperaRegistro11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro11()
    {
        $stSql = "
        SELECT
                '11' AS tipoRegistro
             ,  norma.num_norma as nroLOA";
            if ($this->getDado('tipo') == 1) {
                $stSql .= ",  REPLACE(configuracao_loa.percentual_abertura_credito::VARCHAR, '.', ',') AS percAutorizado
                           ,  1 AS tipoAutorizacao";
            } elseif ($this->getDado('tipo') == 2) {
                $stSql .= ",  REPLACE(configuracao_loa.percentual_contratacao_credito::VARCHAR, '.', ',') AS percAutorizado
                           ,  2 AS tipoAutorizacao";
            } else {
                $stSql .= ",  REPLACE(configuracao_loa.percentual_contratacao_credito_receita::VARCHAR, '.', ',') AS percAutorizado
                           ,  3 AS tipoAutorizacao";
            }
        $stSql .= "
          FROM  tcemg.configuracao_loa
    INNER JOIN  normas.norma
            ON  norma.cod_norma = configuracao_loa.cod_norma
         WHERE  configuracao_loa.exercicio = '".$this->getDado('exercicio')."'
        ";

        return $stSql;
    }
    
    public function __destruct(){}

}

?>
