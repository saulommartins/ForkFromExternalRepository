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
    * Classe de mapeamento da tabela ARRECADACAO.PROCESSO_SUSPENSAO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRProcessoSuspensao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.08
*/

/*
$Log$
Revision 1.6  2006/11/23 18:11:04  marson
Adição do caso de uso de Suspensão.

Revision 1.5  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.PROCESSO_SUSPENSAO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRProcessoSuspensao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRProcessoSuspensao()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.processo_suspensao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_suspensao,timestamp,cod_lancamento');

    $this->AddCampo('cod_suspensao','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('cod_processo','integer',true,'',false,true);
    $this->AddCampo('ano_exercicio','char',true,'4',false,true);
    $this->AddCampo('cod_lancamento','integer',true,'',true,true);
}
}
?>
