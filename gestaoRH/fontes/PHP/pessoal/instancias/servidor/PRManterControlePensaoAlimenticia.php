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
 Página de Processamento de Controle de Pensao Alimenticia
* Data de Criação   : 20/04/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO. 'RPessoalPensao.class.php'                                         );
include_once ( CAM_GRH_PES_NEGOCIO. 'RPessoalServidor.class.php'                                       );

//Define o nome dos arquivos PHP
$stPrograma = 'ManterControlePensaoAlimenticia';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$arRegistros = array();
$arRegAtual  = array();
$obErro = new erro;
$inPos = 0;

// definindo o servidor que ira pagar as pensões
$obRPessoalServidor = new RPessoalServidor;
$obRPessoalServidor->obRCGMPessoaFisica->setNumCGM ( $request->get('inCGM')                 );
$obRPessoalServidor->consultarServidor             ( $rsServidor, false                     );
$obRPessoalServidor->setCodServidor                ( $rsServidor->getCampo( 'cod_servidor') );

$obRDependente = new RPessoalDependente ( $obRPessoalServidor );

$obRPensao = new RPessoalPensao;
$obRPensao->setRPessoalDependente( $obRDependente );

$obTransacao = new Transacao;
$boTransacao = true;

$arRegistros = Sessao::read('aPensoes');
$arExclusoes = Sessao::read('arPensoesExcluidas');

/* cada linha do array diz oque fazer com ela (alterar ou incluir) por isso não
 é preciso usar a variavel stControl como nos outros programas   */
while ( ($inPos < count($arRegistros)) and ( !$obErro->ocorreu() ) ) {
      $arRegAtual = $arRegistros[$inPos];
      $inPos++;
      $obRPensao = new RPessoalPensao;
      $obRPensao->setRPessoalDependente( $obRDependente );

      //alteração ou inclusão
      // dados da tabela pessoal.pensao
      // se este codigo for nulo então será uma inclusao caso contrario será uma alteração
      $obRPensao->setCodPensao                           ( $arRegAtual['cod_pensao'    ] );
      $obRPensao->obRPessoalDependente->setCodDependente ( $arRegAtual['cod_dependente'] );
      $obRPensao->setTipoPensao                          ( $arRegAtual['tipo_pensao'][0] );
      $obRPensao->setInclusao                            ( $arRegAtual['dataInclusao'  ] );
      $obRPensao->setLimite                              ( $arRegAtual['dataLimite'    ] );
      $obRPensao->setPercentual                          ( $arRegAtual['Percentual'    ] );
      $obRPensao->setObservacao                          ( $arRegAtual['OBS'           ] );

      // dados da tabela pessoal.pensao_banco
      // procurando o código da agencia e o do banco
      $obRPensao->obRMONAgencia->obRMONBanco->setNumBanco ( $arRegAtual [ 'codBanco'   ] );
      $obRPensao->obRMONAgencia->setNumAgencia            ( $arRegAtual [ 'codAgencia' ] );
      $obRPensao->obRMONAgencia->listarAgencia ( $rsAgenciaBancaria );

      $obRPensao->setCodAgencia    ( $rsAgenciaBancaria->getCampo('cod_agencia')  );
      $obRPensao->setCodbanco      ( $rsAgenciaBancaria->getCampo('cod_banco')    );
      $obRPensao->setContaCorrente ($arRegAtual['contaCorrente'] );

      if ($arRegAtual['valor']) {
          // dados da tabela pessoal.pensao_valor
          $obRPensao->setValor ( $arRegAtual['valor'] );
      } else {
          // dados da tabela pessoal.pensao_funcao
          $arFuncao = explode('.', $arRegAtual['codFuncao'] );
          $obRPensao->setCodBiblioteca ( $arFuncao[1] );
          $obRPensao->setCodModulo     ( $arFuncao[0] );
          $obRPensao->setCodFuncao     ( $arFuncao[2] );
      }
      if ($arRegAtual['inNumCGMResp']) {
          // tabela pessoal.responsavel_legal
          $obRPensao->setNumCGMResponsavel( $arRegAtual['inNumCGMResp'] );
      }
      // tabela pessoal.pensao_incidencia      
      for ($i = 1; $i<= 6; $i++) {
          if ($arRegAtual['chIncidencia_'.$i]) {
              $obRPensao->addIncidencia ( $i );
          }
      }
      $obErro =  $obRPensao->salvar( $boTransacao );
}

// fazendo as exclusões se houver
if (!$obErro->ocorreu()) {
    $inPos = 0;
    while ( ($inPos < count($arExclusoes)) and ( !$obErro->ocorreu() ) ) {

        $obRPensao->setCodPensao ( $arExclusoes[$inPos]['cod_pensao'] );
        $obRPensao->setTimeStamp ( $arExclusoes[$inPos]['timeStamp' ] );
        $obErro = $obRPensao->excluir($boTransacao);
        $inPos++;
    }
}

if ( !$obErro->ocorreu() ) {
    sistemaLegado::alertaAviso($pgFilt,"Pensões ", "incluir","aviso", Sessao::getId(), "../");
} else {
    sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
}

?>
