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
    * Classe de mapeamento da tabela diarias.tipo_diaria_despesa
    * Data de Criação: 11/08/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.09.01

    $Id: TDiariasTipoDiariaDespesa.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TDiariasTipoDiariaDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDiariasTipoDiariaDespesa()
{
    parent::Persistente();
    $this->setTabela("diarias.tipo_diaria_despesa");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tipo,timestamp');

    $this->AddCampo('cod_tipo' ,'integer'  ,true  ,''   ,true,'TDiariasTipoDiaria');
    $this->AddCampo('cod_conta','integer'  ,true  ,''   ,false,'TOrcamentoContaDespesa');
    $this->AddCampo('exercicio','char'     ,true  ,'4'  ,false,'TOrcamentoContaDespesa');
    $this->AddCampo('timestamp','timestamp',true  ,''   ,true,true);
}
}
?>
