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

/*
    * Classe de mapeamento da tabela tcemg.nota_fiscal_empenho_liquidacao
    * Data de Criação   : 05/02/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: TTCEMGNotaFiscalEmpenhoLiquidacao.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMGNotaFiscalEmpenhoLiquidacao extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGNotaFiscalEmpenhoLiquidacao()
    {
        parent::Persistente();
        $this->setTabela("tcemg.nota_fiscal_empenho_liquidacao");

        $this->setCampoCod('cod_nota');
        $this->setComplementoChave('exercicio , cod_entidade , cod_empenho , cod_nota_liquidacao , exercicio_liquidacao , exercicio_empenho');

        $this->AddCampo('cod_nota'            , 'integer', true, ''    , true , true);
        $this->AddCampo('exercicio'           , 'varchar', true, '4'   , true , true);
        $this->AddCampo('cod_entidade'        , 'integer', true, ''    , true , true);
        $this->AddCampo('cod_empenho'         , 'integer', true, ''    , true , true);
        $this->AddCampo('exercicio_empenho'   , 'varchar', true, '4'   , true , true);
        $this->AddCampo('cod_nota_liquidacao' , 'integer', true, ''    , true , true);
        $this->AddCampo('exercicio_liquidacao', 'varchar', true, '4'   , true , true);
        $this->AddCampo('vl_liquidacao'       , 'numeric', true, '14,2', false, false);
        $this->AddCampo('vl_associado'        , 'numeric', true, '14,2', false, false);
    }
    
    public function __destruct(){}


}

?>
