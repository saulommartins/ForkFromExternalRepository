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
    * Classe de mapeamento da tabela CONTABILIDADE.TRANSFERENCIA_DESPESA
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.04
                    uc-02.01.07
*/

/*
$Log$
Revision 1.7  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeTransferenciaDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeTransferenciaDespesa()
{
    parent::Persistente();
    $this->setTabela('contabilidade.transferencia_despesa');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_entidade,cod_tipo,exercicio,sequencia,tipo,cod_lote,cod_suplementacao');

    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('sequencia','integer',true,'',true,true);
    $this->AddCampo('tipo','char',true,'1',true,true);
    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('cod_suplementacao','integer',true,'',true,true);

}
}
