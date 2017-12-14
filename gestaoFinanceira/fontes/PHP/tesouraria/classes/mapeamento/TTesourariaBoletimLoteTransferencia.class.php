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
* Classe de mapeamento da tabela TESOURARIA.BOLETIM_LOTE
* Data de Criação: 27/02/2007
* @author Analista: Gelson W. Gonçalves
* @author Desenvolvedor: Rodrigo S. Rodrigues
* @package URBEM
* @subpackage

$Revision: 30668 $
$Name$
$Author: hboaventura $
$Date: 2007-07-03 17:17:00 -0300 (Ter, 03 Jul 2007) $
* Casos de uso: uc-02.04.33

*/

/*
$Log$
Revision 1.1  2007/07/03 20:16:49  hboaventura
uc-02.04.33

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTesourariaBoletimLoteTransferencia extends Persistente
{
    public function TTesourariaBoletimLoteTransferencia()
    {
        parent::Persistente();
        $this->setTabela("tesouraria.boletim_lote_transferencia");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_entidade, cod_boletim, cod_lote, timestamp_arrecadacao, cod_arrecadacao');

        $this->AddCampo('exercicio'             , 'char'        , true, '4' , true  , true );
        $this->AddCampo('cod_entidade'          , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('tipo'                  , 'char'        , true, '4' , true  , true );
        $this->AddCampo('cod_lote'              , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('cod_lote_arrecadacao'  , 'integer'     , true, ''  , true  , true );

    }
}
?>
