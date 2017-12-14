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
* Componente ISelectComissao

* Data de CriaÃ§Ã£o: 01/10/2006

* @author Analista: Fernando Zank Correa Evangelista
* @author Desenvolvedor: Fernando Zank Correa Evangelista

Casos de uso: uc-03.05.15

*/

include_once ( CLA_SELECT );

class ISelectDocumento extends Select
{
    public $rsRecordSet;

    public function setRecordset(&$valor) { $this->rsRecordSet = &$valor ;}
    public function getRecordset() { return $this->rsRecordSet;}

    public function ISelectDocumento()
    {

        parent::Select();

        include_once(TLIC."TLicitacaoDocumento.class.php");
        $obMapeamento = new TLicitacaoDocumento();

        if (!$this->rsRecordSet) {
            $this->rsRecordSet = new Recordset;

            $stFiltro = isset($stFiltro) ? $stFiltro : "";
            $obMapeamento->recuperaTodos( $this->rsRecordSet,$stFiltro,' ORDER BY nom_documento ');

        }
        while ( !$this->rsRecordSet->eof() ) {
            Sessao::write("nomFiltro['documento'][".$this->rsRecordSet->getCampo('cod_documento')."]", $this->rsRecordSet->getCampo('nom_documento'));
            $this->rsRecordSet->proximo();
        }

        $this->rsRecordSet->setPrimeiroElemento();

        $this->setRotulo            ( "Documento"                             );
        $this->setName              ( "inCodDocumento"                        );
        $this->setTitle             ( "Selecione o documento da licitação."   );
        $this->setNull              ( true                                    );
        $this->addOption            ( "","Selecione"                          );
        $this->setCampoID           ( "cod_documento"                         );
        $this->setCampoDesc         ( "nom_documento"                         );
        $this->preencheCombo        ( $this->rsRecordSet                      );
        $this->setDefinicao         ("SELECT");
    }
}
?>
