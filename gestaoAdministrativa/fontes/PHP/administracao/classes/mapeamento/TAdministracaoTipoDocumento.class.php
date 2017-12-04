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
    * Classe de mapeamento da tabela ADMINISTRACAO.TIPO_DOCUMENTO
    * Data de Criação: 26/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 21346 $
    $Name$
    $Author: cassiano $
    $Date: 2007-03-27 10:20:18 -0300 (Ter, 27 Mar 2007) $

    * Casos de uso: uc-01.03.100
*/

/*
$Log$
Revision 1.2  2007/03/27 13:20:18  cassiano
Correção no caso de uso

Revision 1.1  2006/09/26 16:02:19  cercato
mapeamento para tipo_documento.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoTipoDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TAdministracaoTipoDocumento()
    {
        parent::Persistente();
        $this->setTabela('ADMINISTRACAO.TIPO_DOCUMENTO');

        $this->setCampoCod('cod_tipo_documento');
        $this->setComplementoChave('');

        $this->AddCampo('cod_tipo_documento', 'integer', true, '', true, false );
        $this->AddCampo('descricao', 'varchar', true, '80', false, false );
    }

} // end of class
