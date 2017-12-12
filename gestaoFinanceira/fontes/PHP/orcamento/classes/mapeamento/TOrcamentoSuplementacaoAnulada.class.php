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
    * Classe de mapeamento da tabela ORCAMENTO_SUPLEMENTACAO_ANULADA
    * Data de Criação: 22/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.24
                    uc-02.01.07
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO_SUPLEMENTACAO_ANULADA
  * Data de Criação: 22/04/2005

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Anderson R. M. Buzo

*/
class TOrcamentoSuplementacaoAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoSuplementacaoAnulada()
{
    parent::Persistente();
    $this->setTabela('orcamento.suplementacao_anulada');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_suplementacao,exercicio');

    $this->AddCampo('cod_suplementacao'         ,'integer' ,true ,''   ,true  ,true );
    $this->AddCampo('exercicio'                 ,'char'    ,true ,'04' ,true  ,true );
    $this->AddCampo('cod_suplementacao_anulacao','integer' ,true ,''   ,false ,true );
}

}
