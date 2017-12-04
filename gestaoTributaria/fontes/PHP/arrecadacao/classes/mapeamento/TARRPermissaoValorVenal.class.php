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
    * Classe de mapeamento da tabela ARRECADACAO.PERMISSAO_VALOR_VENAL
    * Data de Criação: 20/04/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPermissaoValorVenal.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.06
*/

/*
$Log$
Revision 1.3  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.2  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.PERMISSAO_VALOR_VENAL
  * Data de Criação: 20/04/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRPermissaoValorVenal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRPermissaoValorVenal()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.permissao_valor_venal');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm');

    $this->AddCampo('numcgm','integer',true,'',true,true);
}

}
?>
