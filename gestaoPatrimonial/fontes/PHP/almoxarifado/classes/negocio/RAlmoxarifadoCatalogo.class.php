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
    * Classe de Regra de Catálogo
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @package URBEM
    * @subpackage Regra

    $Revision: 16650 $
    $Name$
    $Autor: $
    $Date: 2006-10-11 07:08:19 -0300 (Qua, 11 Out 2006) $

    * Casos de uso: uc-03.03.04 uc-03.03.05
*/

/*
$Log$
Revision 1.27  2006/10/11 10:08:19  larocca
Bug #5796#

Revision 1.26  2006/07/26 13:33:07  leandro.zis
Bug #6623#

Revision 1.25  2006/07/10 19:39:53  rodrigo
Adicionado nos componentes de itens,marca e centro de custa a função ajax para manipulação dos dados.

Revision 1.24  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.23  2006/07/06 12:09:31  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogo.class.php");
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoNivel.class.php");
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php");

/**
    * Classe de Regra de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott
*/

class RAlmoxarifadoCatalogo
{

    /**
        * @access Private
        * @var Object
    */

    public $obTAlmoxarifadoCatalogo;

    /**
        * @access Private
        * @var Integer
    */

    public $inCodigo;

    /**
        * @access Private
        * @var String
    */

    public $stDescricao;

    /**
        * @access Private
        * @var Boolean
    */

    public $boPermiteManutencao;

    /**
        * @access Private
        * @var String
    */

    public $stJs;

    /**
        * @access Public
        * @return Integer
    */

    public function setCodigo($inCodigo) { $this->inCodigo = $inCodigo; }

    /**
        * @access Public
        * @return Integer
    */

    public function setDescricao($stDescricao) { $this->stDescricao = $stDescricao; }

    /**
        * @access Public
        * @return Integer
    */

    public function setPermiteManutencao($boPermiteManutencao) { $this->boPermiteManutencao = $boPermiteManutencao; }

    /**
        * @access Public
        * @return Integer
    */

    public function getCodigo() { return $this->inCodigo; }

    /**
        * @access Public
        * @return String
    */

    public function getDescricao() { return $this->stDescricao; }

    /**
        * @access Public
        * @return Boolean
    */

    public function getPermiteManutencao() { return $this->boPermiteManutencao; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoCatalogo()
    {
        $this->obTransacao  = new Transacao ;
        $this->obTAlmoxarifadoCatalogo = new TAlmoxarifadoCatalogo;
    }

    /**
        * Método Para adicionar Niveis Catalogo
        * @access Public
    */

    public function addCatalogoNivel()
    {
        $this->arCatalogoNivel[] = new RAlmoxarifadoCatalogoNivel( $this );
        $this->roCatalogoNivel = &$this->arCatalogoNivel[ count( $this->arCatalogoNivel ) -1 ];
    }

    /**
        * Método Para adicionar Niveis Catalogo
        * @access Public
    */

    public function checarArrayNivel()
    {
        $obErro = new Erro();

        if ($this->arCatalogoNivel) {
            if (count($this->arCatalogoNivel) < 1) {
                $obErro->setDescricao("O catálogo deve possuir pelo menos um nível.");
            }
        } else {
            $obErro->setDescricao("O catálogo deve possuir pelo menos um nível.");
        }

        return $obErro;
    }

    public function verificarClassificacao(&$obErroRegra, $boTransacao = "")
    {
        $obErroRegra = new Erro;
        $rsClassificacaoNiveis = new RecordSet();
        $obCatalogoClassificacao = new RAlmoxarifadoCatalogoClassificacao();
        $obCatalogoClassificacao->obRAlmoxarifadoCatalogo = &$this;
        $obErro = $obCatalogoClassificacao->listar($rsClassificacaoNiveis, '', $boTransacao);

        if (!$obErro->ocorreu()) {
            if ($rsClassificacaoNiveis->getNumLinhas() > 0) {
                $obErroRegra->setDescricao('Este catálogo possui classificações cadastradas.');
            }
        }

        return $obErro;
    }

    /**
        * Método Para validar Niveis Catalogo
        * @access Public
    */

    public function validarNiveis(&$obErroRegra,$boTransacao = "")
    {
        $obErroRegra = new Erro;
        $rsValidaNivel = new RecordSet();

        $obCatalogoClassificacao = new RAlmoxarifadoCatalogoClassificacao();
        $obCatalogoClassificacao->obRAlmoxarifadoCatalogo = &$this;
        $obErro = $obCatalogoClassificacao->validarNivelClassificacao($rsValidaNivel, $boTransacao);

        if (!$obErro->ocorreu()) {
            if ($rsValidaNivel->getNumLinhas() > 0) {
                $obErroRegra->setDescricao('Este nível possui classificações que excedem a máscara.');
            }
        }

        return $obErro;
    }

    /**
        * Método Para validar Niveis Catalogo
        * @access Public
    */

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listarNaoExcluiveis(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
    {
        $boTransacao = "";
        $this->obTAlmoxarifadoCatalogo->setDado('boNaoExcluiveis', false);

        $obErro = $this->listar( $rsRecordSet, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
    {
        $stFiltro = "";
        $boTransacao = "";
        if ($this->stDescricao) {
            $this->obTAlmoxarifadoCatalogo->setDado('stDescricao', $this->stDescricao );
        }

        $obErro = $this->obTAlmoxarifadoCatalogo->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        $rsRecordSet->addFormatacao('descricao','STRIPSLASHES');

        return $obErro;
    }

    public function listarNiveis(&$rsNiveis, $boTransacao="")
    {
        $obRAlmoxarifadoCatalogoNivel = new RAlmoxarifadoCatalogoNivel( $this );
        $obErro = $obRAlmoxarifadoCatalogoNivel->listar( $rsNiveis, "", $boTransacao );

        return $obErro;
    }

    public function listarNiveisMae(&$rsNiveis, $boTransacao="")
    {
       // $obRAlmoxarifadoCatalogoNivel = new RAlmoxarifadoCatalogoNivel( &$this );
        $obErro = $this->roCatalogoNivel->listarMae( $rsNiveis, "", $boTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaPorChave na classe Persistente
        * @access Public
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function consultar($boTransacao = "")
    {
        $this->obTAlmoxarifadoCatalogo->setDado("cod_catalogo", $this->inCodigo);
        $obErro = $this->obTAlmoxarifadoCatalogo->recuperaPorChave( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $rsRecordSet->addFormatacao('descricao', 'STRIPSLASHES');
            $this->setDescricao($rsRecordSet->getCampo("descricao"));
            $this->setPermiteManutencao( $rsRecordSet->getCampo ( "permite_manutencao" ) == 't');

            $obRAlmoxarifadoCatalogoNivel = new RAlmoxarifadoCatalogoNivel( $this );
            if ($rsRecordSet->getNumLinhas()>=1) {
                $obRAlmoxarifadoCatalogoNivel->listar( $rsNiveis, "", $boTransacao );

                while ( !$rsNiveis->EOF() ) {
                    $this->addCatalogoNivel();
                    $this->roCatalogoNivel->setNivel     ( $rsNiveis->getCampo('nivel')     );
                    $this->roCatalogoNivel->setMascara   ( $rsNiveis->getCampo('mascara')   );
                    $this->roCatalogoNivel->setDescricao ( $rsNiveis->getCampo('descricao' ));
                    $rsNiveis->proximo();
                }
            }
        }

        return $obErro;
    }

    /**
        * Incluir Catalogo
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function incluir($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->validaDescricaoCatalogo( $boValida,$stAcao='incluir',$boTransacao);
            if ($boValida == 'FALSE') {
               $obErro->setDescricao('Este Catálogo já está cadastrado.');
            } else {
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->checarArrayNivel();
                    if (!$obErro->ocorreu()) {
                        $obErro = $this->obTAlmoxarifadoCatalogo->proximoCod( $this->inCodigo, $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            $this->obTAlmoxarifadoCatalogo->setDado( "cod_catalogo"         , $this->inCodigo);
                            $this->obTAlmoxarifadoCatalogo->setDado( "descricao"            , $this->stDescricao );

                            $obErro = $this->obTAlmoxarifadoCatalogo->inclusao( $boTransacao );

                            for ($inPosNivel = 0; $inPosNivel < count($this->arCatalogoNivel); $inPosNivel++) {
                                $obErro = $this->arCatalogoNivel[$inPosNivel]->incluir( $boTransacao );

                                if ($obErro->ocorreu()) {
                                    break;
                                }
                            }
                        }
                    }
                    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogo );
                }
            }
        }

        return $obErro;
    }

    /**
        * Alterar Classificacao
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function alterar($boTransacao = "")
    {
        $boIncluir = false;
        $boFlagTransacao = false;
        $rsNiveis = new RecordSet();
        $obErro = $this->checarArrayNivel();
        if (!$obErro->ocorreu()) {
            $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->validaDescricaoCatalogo( $boValida,$stAcao='alterar',$boTransacao);
                if ($boValida == 'FALSE') {
                    $obErro->setDescricao('Este Catálogo já está cadastrado.');
                } else {
                    if ( !$obErro->ocorreu()) {
                        $this->obTAlmoxarifadoCatalogo->setDado( "cod_catalogo"        , $this->getCodigo()            );
                        $this->obTAlmoxarifadoCatalogo->setDado( "descricao"           , $this->getDescricao()         );
                        $this->obTAlmoxarifadoCatalogo->setDado( "permite_manutencao"  , $this->getPermiteManutencao() );
                        $obErro = $this->verificarClassificacao($obErroVerificar,$boTransacao);
                        for ($inPosNivel = 0; $inPosNivel < count($this->arCatalogoNivel); $inPosNivel++) {
                            if (!$obErro->ocorreu()) {
                                if (!$obErroVerificar->ocorreu()) {
                                    $obErro = $this->roCatalogoNivel->listar($rsNiveis, '', $boTransacao);
                                    if (!$obErro->ocorreu()) {
                                        $arNiveis = $rsNiveis->getElementos();
                                        for ($a = 0; $a < count($arNiveis); $a++) {
                                            $obCatalogoNivel = new RAlmoxarifadoCatalogoNivel( $this );
                                            $obCatalogoNivel->setNivel($arNiveis[$a]['nivel']);
                                            $obErro = $obCatalogoNivel->excluir($boTransacao);
                                        }
                                        for ($a = 0; $a < count($this->arCatalogoNivel); $a++) {
                                            $obErro = $this->arCatalogoNivel[$a]->incluir($boTransacao);
                                        }
                                    }
                                } else {
                                    $this->arCatalogoNivel[$inPosNivel]->alterar($boTransacao);
                                }
                            }
                        }
                        // comentado para testes --- se não houver mais problemas deletar linha!!
                        //$obErro = $this->obTAlmoxarifadoCatalogo->atualizaCodigoEstrutural($boTransacao);
                        if (!$obErro->ocorreu()) {
                            $obErro = $this->obTAlmoxarifadoCatalogo->alteracao( $boTransacao );
                        }
                    }
                }
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogo );
        }

        return $obErro;
    }

    /**
        * Exclui Catalogo
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function excluir($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTAlmoxarifadoCatalogo->setDado( "cod_catalogo"        , $this->inCodigo );

            $obErro = $this->verificarClassificacao($obErroVerificar, $boTransacao);
            if (!$obErro->ocorreu()) {
                $obRAlmoxarifadoCatalogoClassificao = new RAlmoxarifadoCatalogoClassificacao();
                $obRAlmoxarifadoCatalogoClassificao->obRAlmoxarifadoCatalogo->setCodigo($this->getCodigo());
                $obRAlmoxarifadoCatalogoClassificao->listar($rsClassificacoes);
                if ($rsClassificacoes->getNumLinhas() > 0) {
                    $obErro->setDescricao("O Catálogo não pode ser excluído, pois existem classificações relacionadas a ele.");
                }
            }

            if (!$obErroVerificar->ocorreu() && !$obErro->ocorreu()) {
                $obErro = $this->consultar($boTransacao);

                if (!$obErro->ocorreu()) {
                     for ($inPos = 0; $inPos < count($this->arCatalogoNivel); $inPos++) {
                        $obErro = $this->arCatalogoNivel[$inPos]->excluir($boTransacao);

                        if ($obErro->ocorreu()) {
                            break;
                        }
                    }
                    if (!$obErro->ocorreu()) {
                        $obErro = $this->obTAlmoxarifadoCatalogo->exclusao( $boTransacao );
                    }
                }
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogo );

        if (!$obErro->ocorreu()) {
            $obErro = $obErroVerificar;
        }

        return $obErro;
    }

    public function montaValores()
    {
        $this->stJs = "return AdicionaValores('adicionaValor');";

        SistemaLegado::ExecutaFrameOculto($this->stJs);
    }

    public function validaDescricaoCatalogo(&$boValida ,$stAcao ,$boTransacao)
    {
        $stOrder ='';
        $boValida = 'TRUE';
        $obErro = $this->listar ( $rsLista,$stOrder,$boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $rsLista->getNumLinhas() > 0 ) {
                if ($stAcao == 'incluir') {
                    $boValida = 'FALSE';
                } else {
                    while (!$rsLista->eof()) {
                        if ($rsLista->getCampo('cod_catalogo') != $this->getCodigo()  ) {
                            $boValida = 'FALSE';
                        }
                        $rsLista->proximo();
                    }
                }
            }
            $rsLista->setPrimeiroElemento();
            $obErro = $this->listarNaoExcluiveis($rsLista,$stOrder,$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsLista->getNumLinhas() > 0 ) {
                    if ($stAcao == 'incluir') {
                        $boValida = 'FALSE';
                    } else {
                        while (!$rsLista->eof()) {
                            if ($rsLista->getCampo('cod_catalogo') != $this->getCodigo()  ) {
                                $boValida = 'FALSE';
                            }
                            $rsLista->proximo();
                        }
                    }
                }
            }
        }

        return $obErro;
    }

}
