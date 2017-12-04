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
    * Classe de mapeamento da tabela ALMOXARIFADO.DOACAO_EMPRESTIMO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 27467 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-11 09:33:19 -0200 (Sex, 11 Jan 2008) $

    * Casos de uso: uc-03.03.11
*/

/*
$Log$
Revision 1.8  2006/09/15 11:02:43  leandro.zis
Bug #6985#

Revision 1.7  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.DOACAO_EMPRESTIMO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoDoacaoEmprestimo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoDoacaoEmprestimo()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.doacao_emprestimo');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lancamento,cod_item,cod_centro,cod_marca,cod_almoxarifado');

    $this->AddCampo('cod_lancamento','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_processo','integer',true,'',false,true);
    $this->AddCampo('ano_exercicio','char',true,'4',false,true);

}
}
