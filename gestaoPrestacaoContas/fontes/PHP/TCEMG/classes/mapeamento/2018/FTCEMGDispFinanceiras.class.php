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
    * Arquivo de mapeamento para a função que busca os dados da disp financeiras
    * Data de Criação   : 19/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDispFinanceiras extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGDispFinanceiras()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_disp_financeiras');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('bimestre'      ,'integer',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT ".$this->getDado('bimestre')." AS bimestre
                 , caixa
                 , conta_movimento
                 , contas_vinculadas
                 , aplicacoes_financeiras
                 , compromissado
                 , caixa_rpps
                 , contas_movimento_rpps
                 , contas_vinculadas_rpps
                 , aplicacoes_financeiras_rpps
                 , compromissado_rpps
                 , 'S' AS nada_declarar
                 , 0.00 AS caixa_rppsas
                 , 0.00 AS conta_movimento_rppsas
                 , 0.00 AS contas_vinculadas_rppsas
                 , 0.00 AS aplicacoes_financeiras_rppsas
                 , 0.00 AS compromissado_rppsas
                 , 0.00 AS aplicacoes_financeiras_vinc
                 , 0.00 AS aplicacoes_financeiras_vinc_rpps
                 , 0.00 AS aplicacoes_financeiras_vinc_rppsas
              FROM ".$this->getTabela()."('" . $this->getDado('exercicio') . "','" . $this->getDado('cod_entidade') . "','".$this->getDado('dtInicio')."','".$this->getDado('dtFinal')."') AS retorno
                                          ( caixa                       NUMERIC ,
                                            conta_movimento             NUMERIC ,
                                            contas_vinculadas           NUMERIC ,
                                            aplicacoes_financeiras      NUMERIC ,
                                            compromissado               NUMERIC ,
                                            caixa_rpps                  NUMERIC ,
                                            contas_movimento_rpps       NUMERIC ,
                                            contas_vinculadas_rpps      NUMERIC ,
                                            aplicacoes_financeiras_rpps NUMERIC ,
                                            compromissado_rpps          NUMERIC
                                          )";
        return $stSql;
    }

}
