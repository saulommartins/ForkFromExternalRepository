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
    * Classe de mapeamento da tabela ARRECADACAO.VENCIMENTO_PARCELA
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRVencimentoParcela.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.10  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.9  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.VENCIMENTO_PARCELA
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRVencimentoParcela extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRVencimentoParcela()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.vencimento_parcela');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_grupo,cod_vencimento,cod_parcela');

    $this->AddCampo('cod_grupo','integer',true,'',true,true);
    $this->AddCampo('cod_vencimento','integer',true,'',true,true);
    $this->AddCampo('cod_parcela','integer',true,'',true,false);
    $this->AddCampo('data_vencimento','date',true,'',false,false);
    $this->AddCampo('data_vencimento_desconto','date',false,'',false,false);
    $this->AddCampo('valor','float',false,'',false,false);
    $this->AddCampo('percentual','boolean',false,'',false,false);
    $this->AddCampo('ano_exercicio', 'varchar', true, '4', true, true );
}
}
?>
