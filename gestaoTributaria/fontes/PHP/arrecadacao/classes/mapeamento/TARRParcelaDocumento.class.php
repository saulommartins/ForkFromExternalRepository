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
    * Classe de mapeamento da tabela ARRECADACAO.PARCELA_DOCUMENTO
    * Data de Criação: 24/05/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRParcelaDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.1  2007/10/09 18:47:34  cercato
 Ticket#9281#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRParcelaDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRParcelaDocumento()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.parcela_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_parcela,cod_documento,num_documento,exercicio,cod_situacao');

        $this->AddCampo('cod_parcela','integer',true,'',true,true);
        $this->AddCampo('cod_documento','integer',true,'',true,true);
        $this->AddCampo('num_documento','integer',true,'',true,true);
        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('cod_situacao','integer',true,'',false,true);
    }

}
?>
